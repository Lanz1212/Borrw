<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Setting
 * 
 * Merepresentasikan konfigurasi atau pengaturan aplikasi yang disimpan dalam database.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Mengambil nilai pengaturan berdasarkan key.
     * 
     * @param string $key Kunci pengaturan yang dicari.
     * @param string $default Nilai default jika key tidak ditemukan.
     * @return string
     */
    public static function get(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? (string) $setting->value : $default;
    }

    /**
     * Menyimpan atau memperbarui nilai pengaturan.
     * 
     * @param string $key Kunci pengaturan.
     * @param mixed $value Nilai yang akan disimpan.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
