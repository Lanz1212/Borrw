<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller InventoryController
 * 
 * Mengelola inventaris/barang, termasuk proses impor data secara masal.
 */
class InventoryController extends Controller
{
    /**
     * Menampilkan halaman manajemen inventaris.
     */
    public function index()
    {
        return view('inventory.index');
    }

    /**
     * Mengambil data inventaris (API) untuk keperluan tabel/datatable.
     * Dilengkapi dengan fitur pencarian (search) dan filter berdasarkan tipe (type).
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $query = Inventory::query();

        // Fitur pencarian teks bebas
        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tipe barang (misal: 'pinjam' atau 'consumable')
        if ($type = $request->type) {
            $query->where('type', $type);
        }

        $items = $query->orderBy('name')->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    /**
     * Menyimpan data barang baru ke dalam inventaris.
     * Hanya pengguna dengan role admin yang diizinkan melakukan ini.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code'          => 'required|string|unique:inventory,code',
            'name'          => 'required|string',
            'category'      => 'required|string',
            'type'          => 'required|in:pinjam,consumable',
            'total_qty'     => 'required|integer|min:0',
            'available_qty' => 'required|integer|min:0',
            'min_stock'     => 'nullable|integer|min:0',
            'condition'     => 'required|in:baik,rusak,perlu_perbaikan',
            'notes'         => 'nullable|string',
        ]);

        // Logika bisnis: Qty tersedia tidak logis jika lebih besar dari total Qty
        if ($validated['available_qty'] > $validated['total_qty']) {
            return response()->json(['success' => false, 'message' => 'Qty tersedia tidak boleh melebihi total qty.'], 422);
        }

        $item = Inventory::create([
            'code'          => $validated['code'],
            'name'          => $validated['name'],
            'category'      => $validated['category'],
            'type'          => $validated['type'],
            'total_qty'     => $validated['total_qty'],
            'available_qty' => $validated['available_qty'],
            'min_stock'     => $validated['min_stock'] ?? 0,
            'condition'     => $validated['condition'],
            'notes'         => $validated['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan.', 'data' => $item]);
    }

    /**
     * Memperbarui detail barang yang sudah ada.
     * Memastikan kode barang unik kecuali untuk barang yang sedang diupdate.
     * 
     * @param Request $request
     * @param Inventory $inventory
     * @return JsonResponse
     */
    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code'          => 'required|string|unique:inventory,code,' . $inventory->id,
            'name'          => 'required|string',
            'category'      => 'required|string',
            'type'          => 'required|in:pinjam,consumable',
            'total_qty'     => 'required|integer|min:0',
            'available_qty' => 'required|integer|min:0',
            'min_stock'     => 'nullable|integer|min:0',
            'condition'     => 'required|in:baik,rusak,perlu_perbaikan',
            'notes'         => 'nullable|string',
        ]);

        if ($validated['available_qty'] > $validated['total_qty']) {
            return response()->json(['success' => false, 'message' => 'Qty tersedia tidak boleh melebihi total qty.'], 422);
        }

        $inventory->update($validated);

        return response()->json(['success' => true, 'message' => 'Barang berhasil diupdate.', 'data' => $inventory->fresh()]);
    }

    /**
     * Menghapus barang dari inventaris.
     * 
     * @param Inventory $inventory
     * @return JsonResponse
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        $this->authorizeAdmin();
        $inventory->delete();
        return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus.']);
    }

    /**
     * Proses impor masal data inventaris (bulk insert/update).
     * Menerima array data barang, melakukan validasi, lalu menambah atau memperbarui (upsert) data.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $request->validate(['items' => 'required|array|min:1']);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($request->items as $row) {
            $code = trim($row['code'] ?? '');
            $name = trim($row['name'] ?? '');
            // Skip data yang tidak memiliki kode atau nama yang valid
            if (!$code || !$name) { $skipped++; continue; }

            // Sanitasi input dengan memberikan nilai default sesuai enum pada DB
            $type      = in_array($row['type'] ?? '', ['pinjam', 'consumable']) ? $row['type'] : 'pinjam';
            $condition = in_array($row['condition'] ?? '', ['baik', 'rusak', 'perlu_perbaikan']) ? $row['condition'] : 'baik';
            $totalQty  = max(0, (int)($row['total_qty'] ?? 0));
            $minStock  = max(0, (int)($row['min_stock'] ?? 0));
            $hasAvail  = isset($row['available_qty']) && $row['available_qty'] !== null && $row['available_qty'] !== '';
            
            // Logic khusus: Pastikan available qty tidak lebih dari total qty
            $availQty  = $hasAvail ? max(0, min((int)$row['available_qty'], $totalQty)) : $totalQty;

            // Cari apakah barang dengan kode yang sama sudah ada di sistem
            $existing = Inventory::where('code', $code)->first();

            if ($existing) {
                // Update barang existing
                $updateData = [
                    'name'      => $name,
                    'category'  => trim($row['category'] ?? 'Umum'),
                    'type'      => $type,
                    'total_qty' => $totalQty,
                    'min_stock' => $minStock,
                    'condition' => $condition,
                    'notes'     => trim($row['notes'] ?? '') ?: $existing->notes,
                ];
                if ($hasAvail) {
                    $updateData['available_qty'] = $availQty;
                }
                $existing->update($updateData);
                $updated++;
            } else {
                // Buat barang baru jika belum ada
                Inventory::create([
                    'code'          => $code,
                    'name'          => $name,
                    'category'      => trim($row['category'] ?? 'Umum'),
                    'type'          => $type,
                    'total_qty'     => $totalQty,
                    'available_qty' => $availQty,
                    'min_stock'     => $minStock,
                    'condition'     => $condition,
                    'notes'         => trim($row['notes'] ?? '') ?: null,
                ]);
                $created++;
            }
        }

        $msg = "Import selesai: {$created} barang baru ditambahkan, {$updated} barang diperbarui";
        if ($skipped) $msg .= ", {$skipped} baris dilewati";
        $msg .= '.';

        return response()->json(['success' => true, 'message' => $msg, 'created' => $created, 'updated' => $updated]);
    }

    /**
     * Memeriksa apakah user saat ini memiliki akses sebagai Admin.
     * Jika tidak, kembalikan HTTP 403 Forbidden.
     */
    private function authorizeAdmin(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
    }
}
