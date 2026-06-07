<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class PhotoService
{
    /**
     * Simpan foto ke storage publik dengan resize dan kompresi otomatis.
     * Mengembalikan path relatif terhadap disk 'public'.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder  Contoh: 'borrow-photos', 'return-photos', 'damage-photos'
     * @return string
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        $subDir  = $folder . '/' . now()->format('Y/m');
        $filename = now()->format('YmdHis') . '_' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '.webp';
        $path    = $subDir . '/' . $filename;
        $absDir  = storage_path('app/public/' . $subDir);

        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        $image = Image::read($file->getRealPath());

        $w = $image->width();
        $h = $image->height();
        if ($w > 1600 || $h > 1600) {
            if ($w >= $h) {
                $image->scale(width: 1600);
            } else {
                $image->scale(height: 1600);
            }
        }

        $encoded = null;
        try {
            $encoded = $image->toWebp(80);
        } catch (\Throwable $e) {
            $filename = str_replace('.webp', '.jpg', $filename);
            $path     = $subDir . '/' . $filename;
            $encoded  = $image->toJpeg(80);
        }

        Storage::disk('public')->put($subDir . '/' . $filename, (string) $encoded);

        return $path;
    }

    /**
     * Hapus foto dari storage jika ada.
     */
    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Kembalikan URL publik dari path relatif.
     * Root-relative (/storage/...) agar tidak tergantung APP_URL.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) return null;
        return '/storage/' . ltrim($path, '/');
    }
}
