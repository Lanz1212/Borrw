<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryQrController extends Controller
{
    public function show(Inventory $inventory)
    {
        $qrSvg = QrCode::size(280)->errorCorrection('H')->generate($inventory->code);
        return view('inventory.qr', compact('inventory', 'qrSvg'));
    }

    public function printView(Inventory $inventory)
    {
        $qrSvg = QrCode::size(320)->errorCorrection('H')->generate($inventory->code);
        return view('inventory.qr_print', compact('inventory', 'qrSvg'));
    }
}
