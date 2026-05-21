<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transactions.index');
    }

    public function history()
    {
        return view('transactions.history');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Transaction::with('details')->orderByDesc('created_at');

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('borrower_name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->limit(50)->get()->map(function ($t) {
            return [
                'id'           => $t->id,
                'transaction_code' => $t->transaction_code,
                'borrower_id'  => $t->borrower_id,
                'borrower_name' => $t->borrower_name,
                'loan_date'    => $t->loan_date?->toISOString(),
                'return_date'  => $t->return_date?->toISOString(),
                'status'       => $t->status,
                'notes'        => $t->notes,
                'signature'    => $t->signature,
                'created_by_name' => $t->created_by_name,
                'details'      => $t->details->map(fn($d) => [
                    'id'           => $d->id,
                    'inventory_id' => $d->inventory_id,
                    'item_name'    => $d->item_name,
                    'item_code'    => $d->item_code,
                    'item_type'    => $d->item_type,
                    'qty'          => $d->qty,
                    'status'       => $d->status,
                    'qty_returned' => $d->qty_returned,
                    'return_date'  => $d->return_date?->toISOString(),
                ]),
            ];
        });

        return response()->json(['success' => true, 'data' => $transactions]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'borrower_id'   => 'required|exists:borrowers,id',
            'borrower_name' => 'required|string',
            'loan_date'     => 'required|date',
            'cart'          => 'required|array|min:1',
            'cart.*.inventory_id' => 'required|exists:inventory,id',
            'cart.*.qty'    => 'required|integer|min:1',
            'notes'         => 'nullable|string',
            'signature'     => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $cart = $request->cart;

            $allConsumable = true;
            $inventoryMap  = [];

            foreach ($cart as $cartItem) {
                $inv = Inventory::lockForUpdate()->find($cartItem['inventory_id']);
                if (!$inv) {
                    return response()->json(['success' => false, 'message' => "Barang ID {$cartItem['inventory_id']} tidak ditemukan."], 422);
                }
                if ($inv->available_qty < $cartItem['qty']) {
                    return response()->json(['success' => false, 'message' => "Stok \"{$inv->name}\" tidak mencukupi. Tersedia: {$inv->available_qty}"], 422);
                }
                if ($inv->type === 'pinjam') {
                    $allConsumable = false;
                }
                $inventoryMap[$inv->id] = $inv;
            }

            $user   = auth()->user();
            $code   = 'TRX-' . now()->format('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $status = $allConsumable ? 'selesai' : 'aktif';

            $transaction = Transaction::create([
                'transaction_code' => $code,
                'borrower_id'      => $request->borrower_id,
                'borrower_name'    => $request->borrower_name,
                'loan_date'        => $request->loan_date,
                'status'           => $status,
                'notes'            => $request->notes,
                'created_by'       => $user->id,
                'created_by_name'  => $user->name,
                'signature'        => $request->signature,
            ]);

            foreach ($cart as $cartItem) {
                $inv        = $inventoryMap[$cartItem['inventory_id']];
                $detailStatus = $inv->type === 'pinjam' ? 'dipinjam' : 'dipakai';

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'inventory_id'   => $inv->id,
                    'item_name'      => $inv->name,
                    'item_code'      => $inv->code,
                    'item_type'      => $inv->type,
                    'qty'            => $cartItem['qty'],
                    'status'         => $detailStatus,
                    'qty_returned'   => 0,
                ]);

                $inv->decrement('available_qty', $cartItem['qty']);
            }

            return response()->json([
                'success'         => true,
                'message'         => 'Transaksi berhasil dibuat.',
                'transaction_code' => $code,
            ]);
        });
    }

    public function active(): JsonResponse
    {
        $transactions = Transaction::with(['details' => function ($q) {
            $q->where('item_type', 'pinjam')->where('status', 'dipinjam');
        }])
        ->whereHas('details', function ($q) {
            $q->where('item_type', 'pinjam')->where('status', 'dipinjam');
        })
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($t) {
            return [
                'id'           => $t->id,
                'transaction_code' => $t->transaction_code,
                'borrower_id'  => $t->borrower_id,
                'borrower_name' => $t->borrower_name,
                'loan_date'    => $t->loan_date?->toISOString(),
                'return_date'  => $t->return_date?->toISOString(),
                'status'       => $t->status,
                'notes'        => $t->notes,
                'details'      => $t->details->map(fn($d) => [
                    'id'           => $d->id,
                    'inventory_id' => $d->inventory_id,
                    'item_name'    => $d->item_name,
                    'item_code'    => $d->item_code,
                    'item_type'    => $d->item_type,
                    'qty'          => $d->qty,
                    'status'       => $d->status,
                    'qty_returned' => $d->qty_returned,
                ]),
            ];
        });

        return response()->json(['success' => true, 'data' => $transactions]);
    }
}
