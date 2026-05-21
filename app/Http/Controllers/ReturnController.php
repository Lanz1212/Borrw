<?php

namespace App\Http\Controllers;

use App\Models\DamagedItem;
use App\Models\Inventory;
use App\Models\ItemReturn;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        return view('returns.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items'          => 'required|array|min:1',
            'items.*.detail_id'    => 'required|exists:transaction_details,id',
            'items.*.qty_returned' => 'required|integer|min:1',
            'items.*.qty_damaged'  => 'nullable|integer|min:0',
            'items.*.qty_lost'     => 'nullable|integer|min:0',
            'items.*.notes'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $user        = auth()->user();
            $transaction = Transaction::find($request->transaction_id);

            foreach ($request->items as $item) {
                $detail     = TransactionDetail::lockForUpdate()->find($item['detail_id']);
                $qtyReturned = (int) $item['qty_returned'];
                $qtyDamaged  = (int) ($item['qty_damaged'] ?? 0);
                $qtyLost     = (int) ($item['qty_lost'] ?? 0);
                $qtyGood     = $qtyReturned - $qtyDamaged - $qtyLost;

                if ($qtyGood < 0) {
                    return response()->json(['success' => false, 'message' => "Rusak + hilang melebihi jumlah kembali untuk \"{$detail->item_name}\"."], 422);
                }

                $newQtyReturned = $detail->qty_returned + $qtyReturned;
                $newStatus      = $newQtyReturned >= $detail->qty ? 'kembali' : 'partial';

                $detail->update([
                    'qty_returned' => $newQtyReturned,
                    'status'       => $newStatus,
                    'return_date'  => now(),
                ]);

                ItemReturn::create([
                    'transaction_id'        => $transaction->id,
                    'transaction_detail_id' => $detail->id,
                    'inventory_id'          => $detail->inventory_id,
                    'item_name'             => $detail->item_name,
                    'qty_returned'          => $qtyReturned,
                    'qty_good'              => $qtyGood,
                    'qty_damaged'           => $qtyDamaged,
                    'qty_lost'              => $qtyLost,
                    'condition'             => $item['notes'] ?? 'baik',
                    'notes'                 => $item['notes'] ?? null,
                    'processed_by'          => $user->id,
                    'processed_by_name'     => $user->name,
                ]);

                if ($detail->inventory_id) {
                    $inv = Inventory::lockForUpdate()->find($detail->inventory_id);
                    if ($inv) {
                        $inv->increment('available_qty', $qtyGood);
                        if ($qtyDamaged > 0) {
                            $inv->decrement('total_qty', $qtyDamaged);

                            DamagedItem::create([
                                'inventory_id'     => $inv->id,
                                'item_name'        => $inv->name,
                                'qty'              => $qtyDamaged,
                                'description'      => 'Dari pengembalian transaksi ' . $transaction->transaction_code,
                                'transaction_id'   => $transaction->id,
                                'reported_by'      => $user->id,
                                'reported_by_name' => $user->name,
                            ]);
                        }
                    }
                }
            }

            $this->updateTransactionStatus($transaction);

            $transaction->update(['return_date' => now()]);

            return response()->json(['success' => true, 'message' => 'Pengembalian berhasil dicatat.']);
        });
    }

    private function updateTransactionStatus(Transaction $transaction): void
    {
        $pinjamDetails = $transaction->details()->where('item_type', 'pinjam')->get();

        if ($pinjamDetails->isEmpty()) {
            return;
        }

        $allReturned = $pinjamDetails->every(fn($d) => $d->status === 'kembali');
        $anyReturned = $pinjamDetails->some(fn($d) => $d->status === 'kembali');

        $status = $allReturned ? 'selesai' : ($anyReturned ? 'partial' : 'aktif');
        $transaction->update(['status' => $status]);
    }
}
