<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller TransactionController
 * 
 * Mengelola proses pembuatan, persetujuan, dan pencatatan transaksi peminjaman barang.
 */
class TransactionController extends Controller
{
    /**
     * Menampilkan halaman manajemen transaksi aktif.
     */
    public function index()
    {
        return view('transactions.index');
    }

    /**
     * Menampilkan halaman riwayat transaksi (sudah selesai/ditolak).
     */
    public function history()
    {
        return view('transactions.history');
    }

    /**
     * Mengambil data daftar transaksi (API) untuk tabel.
     * Termasuk memuat (eager-load) relasi details dan item returns.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $query = Transaction::with(['details.returns'])->orderByDesc('created_at');

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Pencarian transaksi berdasarkan kode transaksi atau nama peminjam
        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('borrower_name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->limit(50)->get()->map(function ($t) {
            // Memformat output agar lebih mudah dikonsumsi frontend (termasuk mapping relasi details)
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
                    'qty_good'     => $d->returns->sum('qty_good'),
                    'qty_consumed' => $d->returns->sum('qty_consumed'),
                    'qty_damaged'  => $d->returns->sum('qty_damaged'),
                    'qty_lost'     => $d->returns->sum('qty_lost'),
                    'return_notes' => $d->returns->pluck('notes')->filter()->join('; '),
                    'return_date'  => $d->return_date?->toISOString(),
                ]),
            ];
        });

        return response()->json(['success' => true, 'data' => $transactions]);
    }

    /**
     * Membuat transaksi peminjaman baru (Checkout Cart).
     * Jika admin: Langsung diproses (stok dikurangi).
     * Jika user biasa: Masuk status 'menunggu_persetujuan' (stok belum dikurangi).
     * 
     * @param Request $request
     * @return JsonResponse
     */
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

        $isAdmin = auth()->user()->isAdmin();

        // Menggunakan Database Transaction untuk mencegah inkonsistensi data jika insert gagal
        return DB::transaction(function () use ($request, $isAdmin) {
            $cart = $request->cart;

            $allConsumable = true;
            $inventoryMap  = [];

            // Pra-validasi ketersediaan stok
            foreach ($cart as $cartItem) {
                // lockForUpdate digunakan agar stok barang terkunci dan tidak dimodifikasi proses lain pada waktu bersamaan
                $inv = Inventory::lockForUpdate()->find($cartItem['inventory_id']);
                if (!$inv) {
                    return response()->json(['success' => false, 'message' => "Barang ID {$cartItem['inventory_id']} tidak ditemukan."], 422);
                }
                if ($inv->available_qty < $cartItem['qty']) {
                    return response()->json(['success' => false, 'message' => "Stok \"{$inv->name}\" tidak mencukupi. Tersedia: {$inv->available_qty}"], 422);
                }
                if ($inv->type === 'pinjam' || $inv->type === 'bon') {
                    $allConsumable = false;
                }
                $inventoryMap[$inv->id] = $inv;
            }

            $user   = auth()->user();
            // Generate kode transaksi unik otomatis
            $code   = 'TRX-' . now()->format('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            // Logika bisnis:
            // Admin: langsung aktif/selesai. User: menunggu persetujuan admin.
            $status = $isAdmin
                ? ($allConsumable ? 'selesai' : 'aktif')
                : 'menunggu_persetujuan';

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

            // Menyimpan detail transaksi untuk masing-masing barang (cart)
            foreach ($cart as $cartItem) {
                $inv          = $inventoryMap[$cartItem['inventory_id']];
                $detailStatus = ($inv->type === 'pinjam' || $inv->type === 'bon') ? 'dipinjam' : 'dipakai';

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

                // Stok hanya dikurangi secara langsung jika admin yang membuat transaksi (langsung aktif)
                if ($isAdmin) {
                    $inv->decrement('available_qty', $cartItem['qty']);
                }
            }

            $message = $isAdmin
                ? 'Transaksi berhasil dibuat.'
                : 'Transaksi diajukan dan menunggu persetujuan admin.';

            return response()->json([
                'success'          => true,
                'message'          => $message,
                'transaction_code' => $code,
                'pending'          => !$isAdmin,
            ]);
        });
    }

    /**
     * Mengambil daftar transaksi (API) yang masih berstatus menunggu persetujuan.
     * 
     * @return JsonResponse
     */
    public function pending(): JsonResponse
    {
        $transactions = Transaction::with('details')
            ->where('status', 'menunggu_persetujuan')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($t) => [
                'id'               => $t->id,
                'transaction_code' => $t->transaction_code,
                'borrower_name'    => $t->borrower_name,
                'loan_date'        => $t->loan_date?->toISOString(),
                'notes'            => $t->notes,
                'created_by_name'  => $t->created_by_name,
                'details'          => $t->details->map(fn($d) => [
                    'item_name' => $d->item_name,
                    'item_code' => $d->item_code,
                    'item_type' => $d->item_type,
                    'qty'       => $d->qty,
                ]),
            ]);

        return response()->json(['success' => true, 'data' => $transactions]);
    }

    /**
     * Menyetujui transaksi (approve) yang masih pending.
     * Proses ini akan secara resmi mengurangi stok barang.
     * 
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function approve(Transaction $transaction): JsonResponse
    {
        if ($transaction->status !== 'menunggu_persetujuan') {
            return response()->json(['success' => false, 'message' => 'Transaksi bukan dalam status menunggu persetujuan.'], 422);
        }

        return DB::transaction(function () use ($transaction) {
            $allConsumable = true;

            // Validasi ulang stok sebelum persetujuan karena stok bisa saja berkurang selama transaksi pending
            foreach ($transaction->details as $detail) {
                $inv = Inventory::lockForUpdate()->find($detail->inventory_id);
                if (!$inv) {
                    return response()->json(['success' => false,
                        'message' => "Barang \"{$detail->item_name}\" tidak ditemukan di inventori."], 422);
                }
                if ($inv->available_qty < $detail->qty) {
                    return response()->json(['success' => false,
                        'message' => "Stok \"{$inv->name}\" tidak mencukupi. Tersedia: {$inv->available_qty}"], 422);
                }
                if ($inv->type === 'pinjam' || $inv->type === 'bon') {
                    $allConsumable = false;
                }
            }

            // Kurangi stok barang karena transaksi disetujui
            foreach ($transaction->details as $detail) {
                $inv = Inventory::find($detail->inventory_id);
                if ($inv) {
                    $inv->decrement('available_qty', $detail->qty);
                }
            }

            // Update status transaksi menjadi aktif atau langsung selesai (jika hanya berisi barang habis pakai/consumable)
            $transaction->update([
                'status'          => $allConsumable ? 'selesai' : 'aktif',
                'created_by_name' => $transaction->created_by_name . ' (disetujui)',
            ]);

            return response()->json(['success' => true, 'message' => 'Transaksi disetujui dan stok telah dikurangi.']);
        });
    }

    /**
     * Menolak (reject) transaksi yang pending.
     * 
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function reject(Transaction $transaction): JsonResponse
    {
        if ($transaction->status !== 'menunggu_persetujuan') {
            return response()->json(['success' => false, 'message' => 'Transaksi bukan dalam status menunggu persetujuan.'], 422);
        }

        $transaction->update(['status' => 'ditolak']);

        // Update semua detail barang menjadi 'ditolak' agar tidak tampil sebagai "dipakai" / "dipinjam"
        TransactionDetail::where('transaction_id', $transaction->id)
            ->update(['status' => 'ditolak']);

        return response()->json(['success' => true, 'message' => 'Transaksi ditolak.']);
    }

    /**
     * Mengambil daftar transaksi (API) yang berstatus aktif atau partial (barang sedang dipinjam).
     * Sering digunakan untuk fitur pengembalian barang.
     * 
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $transactions = Transaction::with(['details' => function ($q) {
            $q->whereIn('item_type', ['pinjam', 'bon'])->where('status', 'dipinjam');
        }])
        ->whereIn('status', ['aktif', 'partial'])
        ->whereHas('details', function ($q) {
            $q->whereIn('item_type', ['pinjam', 'bon'])->where('status', 'dipinjam');
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
