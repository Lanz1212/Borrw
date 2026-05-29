<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller SettingController
 * 
 * Mengelola konfigurasi aplikasi, serta fungsionalitas backup dan restore database.
 */
class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan aplikasi.
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Mengambil semua pengaturan yang tersimpan di database dalam bentuk key-value pair.
     * 
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json(['success' => true, 'data' => $settings]);
    }

    /**
     * Memperbarui pengaturan aplikasi.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'app_name'        => 'nullable|string|max:100',
            'categories'      => 'nullable|string',
            'session_timeout' => 'nullable|integer|min:1|max:1440',
            'multi_login'     => 'nullable|in:0,1',
            'theme'           => 'nullable|in:orange,blue,green,dark',
            'dark_mode'       => 'nullable|in:0,1',
        ]);

        $keys = ['app_name', 'categories', 'session_timeout', 'multi_login', 'theme', 'dark_mode'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, (string) $request->input($key, ''));
            }
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
    }

    /**
     * Mengunduh file backup (dump) dari seluruh database.
     * 
     * @return \Illuminate\Http\Response
     */
    public function backup(): \Illuminate\Http\Response
    {
        $dbName   = config('database.connections.mysql.database');
        $fileName = 'backup_' . $dbName . '_' . now()->format('Ymd_His') . '.sql';
        $sql      = $this->generateSqlDump();

        return response($sql, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length'      => mb_strlen($sql, '8bit'),
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ]);
    }

    /**
     * Menghasilkan teks SQL mentah untuk seluruh struktur tabel dan data.
     * Proses otomatis ini membuat query DROP TABLE, CREATE TABLE, dan INSERT.
     * 
     * @return string
     */
    private function generateSqlDump(): string
    {
        $out    = [];
        $dbName = config('database.connections.mysql.database');

        $out[] = '-- Borrw Database Backup';
        $out[] = '-- Generated   : ' . now()->toDateTimeString();
        $out[] = '-- Database    : ' . $dbName;
        $out[] = '';
        $out[] = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";';
        $out[] = 'SET AUTOCOMMIT = 0;';
        $out[] = 'START TRANSACTION;';
        $out[] = 'SET time_zone = "+00:00";';
        $out[] = 'SET FOREIGN_KEY_CHECKS = 0;';
        $out[] = '';

        $tableKey = 'Tables_in_' . $dbName;
        $tables   = DB::select('SHOW TABLES');

        foreach ($tables as $tableRow) {
            $arr   = (array) $tableRow;
            $table = $arr[$tableKey] ?? array_values($arr)[0];

            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql    = $createResult[0]->{'Create Table'};

            $out[] = '-- --------------------------------------------------------';
            $out[] = "-- Table: `{$table}`";
            $out[] = '-- --------------------------------------------------------';
            $out[] = '';
            $out[] = "DROP TABLE IF EXISTS `{$table}`;";
            $out[] = $createSql . ';';
            $out[] = '';

            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) continue;

            $firstRow = (array) $rows->first();
            $cols     = '`' . implode('`, `', array_keys($firstRow)) . '`';
            $out[]    = "-- Data for `{$table}`";

            // Melakukan chunking agar proses dump data yang besar tidak menyebabkan memory limit
            foreach ($rows->chunk(500) as $chunk) {
                $valLines = $chunk->map(function ($row) {
                    $escaped = array_map(function ($v) {
                        if ($v === null) return 'NULL';
                        if (is_int($v) || is_float($v)) return $v;
                        return "'" . str_replace(
                            ['\\', "'", "\n", "\r", "\x1a"],
                            ['\\\\', "\\'", '\\n', '\\r', '\\Z'],
                            (string) $v
                        ) . "'";
                    }, (array) $row);
                    return '  (' . implode(', ', $escaped) . ')';
                })->implode(",\n");

                $out[] = "INSERT INTO `{$table}` ({$cols}) VALUES";
                $out[] = $valLines . ';';
                $out[] = '';
            }
        }

        $out[] = 'SET FOREIGN_KEY_CHECKS = 1;';
        $out[] = 'COMMIT;';
        $out[] = '';
        $out[] = '-- End of backup';

        return implode("\n", $out);
    }

    /**
     * Memulihkan (restore) database dari file SQL yang diunggah.
     * Menggunakan metode DB::unprepared untuk mengeksekusi multiple statement.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function restore(Request $request): JsonResponse
    {
        $request->validate(['sql_file' => 'required|file|max:102400']);

        $file = $request->file('sql_file');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (! in_array($ext, ['sql', 'txt'])) {
            return response()->json(['success' => false, 'message' => 'File harus berekstensi .sql'], 422);
        }

        $sql = file_get_contents($file->getRealPath());
        if (empty(trim($sql))) {
            return response()->json(['success' => false, 'message' => 'File SQL kosong.'], 422);
        }

        try {
            // Nonaktifkan pemeriksaan foreign key sementara agar proses restore tidak gagal
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Memisahkan query berdasarkan karakter titik koma (;)
            $statements = array_filter(
                array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)),
                fn ($s) => ! empty($s) && ! str_starts_with($s, '--') && ! str_starts_with($s, '/*')
            );

            foreach ($statements as $stmt) {
                try {
                    DB::unprepared($stmt);
                } catch (\Exception $ignored) {
                    // Abaikan error pada statement tertentu (misalnya data sudah ada)
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            return response()->json(['success' => true, 'message' => 'Database berhasil di-restore. Silakan refresh halaman.']);
        } catch (\Exception $e) {
            try { DB::statement('SET FOREIGN_KEY_CHECKS = 1'); } catch (\Exception $ignored) {}
            return response()->json(['success' => false, 'message' => 'Restore gagal: ' . $e->getMessage()], 500);
        }
    }
}
