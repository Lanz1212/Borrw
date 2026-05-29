<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Controller InventoryQrController
 * 
 * Mengelola pembuatan dan tampilan QR Code untuk barang inventaris.
 */
class InventoryQrController extends Controller
{
    /**
     * Menampilkan QR Code untuk satu barang secara spesifik.
     * 
     * @param Inventory $inventory
     */
    public function show(Inventory $inventory)
    {
        // Menghasilkan QR Code SVG berukuran 280px dengan level error correction High
        $qrSvg = QrCode::size(280)->errorCorrection('H')->generate($inventory->code);
        return view('inventory.qr', compact('inventory', 'qrSvg'));
    }

    /**
     * Menampilkan halaman khusus untuk mencetak (print) QR Code barang.
     * 
     * @param Inventory $inventory
     */
    public function printView(Inventory $inventory)
    {
        $qrSvg = QrCode::size(320)->errorCorrection('H')->generate($inventory->code);
        return view('inventory.qr_print', compact('inventory', 'qrSvg'));
    }
}
