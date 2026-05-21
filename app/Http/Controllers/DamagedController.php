<?php

namespace App\Http\Controllers;

use App\Models\DamagedItem;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamagedController extends Controller
{
    public function index()
    {
        return view('damaged.index');
    }

    public function history()
    {
        return view('damaged.history');
    }

    public function data(): JsonResponse
    {
        $items = DamagedItem::with('transaction')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($d) {
                return [
                    'id'           => $d->id,
                    'inventory_id' => $d->inventory_id,
                    'item_name'    => $d->item_name,
                    'qty'          => $d->qty,
                    'description'  => $d->description,
                    'date'         => $d->created_at?->toISOString(),
                    'reported_by_name' => $d->reported_by_name,
                    'transaction_id'   => $d->transaction_id,
                    'transaction_code' => $d->transaction?->transaction_code,
                    'borrower_name'    => $d->transaction?->borrower_name,
                    'loan_date'        => $d->transaction?->loan_date?->toISOString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'qty'          => 'required|integer|min:1',
            'description'  => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $inv  = Inventory::lockForUpdate()->find($request->inventory_id);

            $qty = (int) $request->qty;
            $newTotal     = max(0, $inv->total_qty - $qty);
            $newAvailable = max(0, $inv->available_qty - $qty);

            $inv->update([
                'total_qty'     => $newTotal,
                'available_qty' => $newAvailable,
            ]);

            $damaged = DamagedItem::create([
                'inventory_id'     => $inv->id,
                'item_name'        => $inv->name,
                'qty'              => $qty,
                'description'      => $request->description,
                'reported_by'      => $user->id,
                'reported_by_name' => $user->name,
            ]);

            return response()->json(['success' => true, 'message' => 'Barang rusak berhasil dicatat.', 'data' => $damaged]);
        });
    }
}
