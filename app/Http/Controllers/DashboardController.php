<?php

namespace App\Http\Controllers;

use App\Models\DamagedItem;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function stats(): JsonResponse
    {
        $inventory = Inventory::all();

        $totalItems    = $inventory->count();
        $availableItems = $inventory->sum('available_qty');
        $borrowedItems  = (int) TransactionDetail::whereHas('transaction', function ($q) {
                $q->whereIn('status', ['aktif', 'partial']);
            })
            ->where('item_type', 'pinjam')
            ->sum(\DB::raw('qty - COALESCE(qty_returned, 0)'));
        $lowStock = $inventory->filter(fn($i) => $i->min_stock > 0 && $i->available_qty <= $i->min_stock)
            ->map(fn($i) => ['name' => $i->name, 'available' => $i->available_qty, 'minStock' => $i->min_stock])
            ->values();

        $totalDamaged = DamagedItem::sum('qty');

        $days = [];
        for ($i = 29; $i >= 0; $i--) {
            $d   = now()->subDays($i);
            $key = $d->toDateString();
            $days[$key] = [
                'label'      => $d->locale('id')->isoFormat('D MMM'),
                'pinjam'     => 0,
                'consumable' => 0,
            ];
        }

        $details = TransactionDetail::with('transaction')
            ->whereHas('transaction', function ($q) {
                $q->where('loan_date', '>=', now()->subDays(30)->startOfDay());
            })
            ->get();

        foreach ($details as $detail) {
            $key = $detail->transaction->loan_date->toDateString();
            if (isset($days[$key])) {
                if ($detail->item_type === 'pinjam') {
                    $days[$key]['pinjam'] += $detail->qty;
                } else {
                    $days[$key]['consumable'] += $detail->qty;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'totalItems'    => $totalItems,
                'availableItems' => $availableItems,
                'borrowedItems'  => $borrowedItems,
                'totalDamaged'   => $totalDamaged,
                'lowStock'       => $lowStock,
                'dailyStats'     => array_values($days),
            ],
        ]);
    }
}
