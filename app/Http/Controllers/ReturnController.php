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

/**
 * Controller ReturnController
 * 
 * Mengelola proses pengembalian barang dari peminjam, termasuk pencatatan kondisi barang dan penyesuaian stok.
 */
class ReturnController extends Controller
{
    /**
     * Menampilkan halaman proses pengembalian barang.
     */
    public function index()
    {
        return view('returns.index');
    }

    /**
     * Memproses form submit pengembalian barang.
     * Mengelola penambahan stok (barang baik) maupun pengurangan stok (barang rusak/hilang).
     * Dibungkus dengan Database Transaction untuk keamanan data.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id'        => 'required|exists:transactions,id',
            'items'                 => 'required|array|min:1',
            'items.*.detail_id'     => 'required|exists:transaction_details,id',
            'items.*.qty_returned'  => 'required|integer|min:0',
            'items.*.qty_consumed'  => 'nullable|integer|min:0',
            'items.*.qty_damaged'   => 'nullable|integer|min:0',
            'items.*.qty_lost'      => 'nullable|integer|min:0',
            'items.*.notes'         => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $user        = auth()->user();
            $transaction = Transaction::find($request->transaction_id);

            foreach ($request->items as $item) {
                // Mengunci baris detail transaksi untuk mencegah update simultan (race conditions)
                $detail     = TransactionDetail::lockForUpdate()->find($item['detail_id']);
                $isBon      = $detail->item_type === 'bon';
                $qtyDamaged = (int) ($item['qty_damaged'] ?? 0);
                $qtyLost    = (int) ($item['qty_lost'] ?? 0);
                $rem        = $detail->qty - $detail->qty_returned;

                if ($isBon) {
                    // === Alur BON: kembali + dipakai + rusak + hilang harus = sisa pinjam ===
                    $qtyKembali  = (int) ($item['qty_returned'] ?? 0);
                    $qtyConsumed = (int) ($item['qty_consumed'] ?? 0);
                    $total       = $qtyKembali + $qtyConsumed + $qtyDamaged + $qtyLost;

                    if ($total !== $rem) {
                        return response()->json(['success' => false, 'message' => "Total pengembalian BON untuk \"{$detail->item_name}\" ({$total}) harus sama dengan sisa pinjam ({$rem})."], 422);
                    }

                    $newQtyReturned = $detail->qty_returned + $total;

                    if ($newQtyReturned >= $detail->qty) {
                        $newStatus = $qtyKembali > 0 ? 'kembali' : 'dipakai';
                    } else {
                        $newStatus = 'partial';
                    }

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
                        'qty_returned'          => $qtyKembali,
                        'qty_good'              => $qtyKembali,
                        'qty_consumed'          => $qtyConsumed,
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
                            // Barang kembali ke stok tersedia
                            if ($qtyKembali > 0) {
                                $inv->increment('available_qty', $qtyKembali);
                            }
                            // Barang dipakai: available_qty sudah dikurangi saat dipinjam, total_qty tetap
                            // Barang rusak: kurangi total_qty dan catat ke tabel kerusakan
                            if ($qtyDamaged > 0) {
                                $inv->decrement('total_qty', $qtyDamaged);

                                DamagedItem::create([
                                    'inventory_id'     => $inv->id,
                                    'item_name'        => $inv->name,
                                    'qty'              => $qtyDamaged,
                                    'description'      => 'Dari pengembalian BON transaksi ' . $transaction->transaction_code,
                                    'condition_notes'  => $item['notes'] ?? null,
                                    'transaction_id'   => $transaction->id,
                                    'reported_by'      => $user->id,
                                    'reported_by_name' => $user->name,
                                ]);
                            }
                        }
                    }
                } else {
                    // === Alur PINJAM biasa ===
                    $qtyReturned = (int) $item['qty_returned'];

                    // Barang dianggap "baik" jika dikembalikan tapi tidak rusak/hilang
                    $qtyGood = $qtyReturned - $qtyDamaged - $qtyLost;

                    // Validasi logis: jumlah rusak dan hilang tidak boleh melebihi jumlah barang yang dikembalikan
                    if ($qtyGood < 0) {
                        return response()->json(['success' => false, 'message' => "Rusak + hilang melebihi jumlah kembali untuk \"{$detail->item_name}\"."], 422);
                    }

                    // Kalkulasi total yang telah dikembalikan sejauh ini
                    $newQtyReturned = $detail->qty_returned + $qtyReturned;
                    $newStatus      = $newQtyReturned >= $detail->qty ? 'kembali' : 'partial';

                    $detail->update([
                        'qty_returned' => $newQtyReturned,
                        'status'       => $newStatus,
                        'return_date'  => now(),
                    ]);

                    // Mencatat data riwayat pengembalian per item (historical data)
                    ItemReturn::create([
                        'transaction_id'        => $transaction->id,
                        'transaction_detail_id' => $detail->id,
                        'inventory_id'          => $detail->inventory_id,
                        'item_name'             => $detail->item_name,
                        'qty_returned'          => $qtyReturned,
                        'qty_good'              => $qtyGood,
                        'qty_consumed'          => 0,
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
                            // Tambahkan kembali qty barang yang kondisinya baik (layak pakai)
                            $inv->increment('available_qty', $qtyGood);

                            // Jika ada barang yang rusak, catat ke tabel kerusakan dan kurangi total kapasitas inventaris
                            if ($qtyDamaged > 0) {
                                $inv->decrement('total_qty', $qtyDamaged);

                                DamagedItem::create([
                                    'inventory_id'     => $inv->id,
                                    'item_name'        => $inv->name,
                                    'qty'              => $qtyDamaged,
                                    'description'      => 'Dari pengembalian transaksi ' . $transaction->transaction_code,
                                    'condition_notes'  => $item['notes'] ?? null,
                                    'transaction_id'   => $transaction->id,
                                    'reported_by'      => $user->id,
                                    'reported_by_name' => $user->name,
                                ]);
                            }
                        }
                    }
                }
            }

            // Memperbarui status transaksi induk (apakah masih aktif/partial atau sudah selesai total)
            $this->updateTransactionStatus($transaction);

            DB::table('transactions')->where('id', $transaction->id)->update(['return_date' => now(), 'updated_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Pengembalian berhasil dicatat.']);
        });
    }

    /**
     * Memeriksa dan memperbarui status transaksi secara keseluruhan
     * berdasarkan status pengembalian setiap barang (details).
     * 
     * @param Transaction $transaction
     */
    private function updateTransactionStatus(Transaction $transaction): void
    {
        // Memproses item bertipe 'pinjam' dan 'bon' (keduanya perlu dikembalikan)
        $returnableDetails = $transaction->details()->whereIn('item_type', ['pinjam', 'bon'])->get();

        if ($returnableDetails->isEmpty()) {
            return;
        }

        // Status final yang dianggap "sudah selesai" per detail:
        // pinjam → 'kembali' | bon → 'kembali' atau 'dipakai'
        $finalStates = ['kembali', 'dipakai'];

        $allReturned = $returnableDetails->every(fn($d) => in_array($d->status, $finalStates));
        $anyReturned = $returnableDetails->some(fn($d) => in_array($d->status, $finalStates));

        $status = $allReturned ? 'selesai' : ($anyReturned ? 'partial' : 'aktif');
        DB::table('transactions')->where('id', $transaction->id)->update(['status' => $status, 'updated_at' => now()]);
    }
}
