<?php

namespace App\Http\Controllers;

use App\Models\DamagedItem;
use App\Models\Inventory;
use App\Services\PhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller DamagedController
 * 
 * Mengelola pelaporan dan pencatatan barang rusak, termasuk penyesuaian stok secara otomatis.
 */
class DamagedController extends Controller
{
    /**
     * Menampilkan halaman formulir untuk melaporkan barang rusak.
     */
    public function index()
    {
        return view('damaged.index');
    }

    /**
     * Menampilkan halaman riwayat pelaporan barang rusak.
     */
    public function history()
    {
        return view('damaged.history');
    }

    /**
     * Mengambil daftar riwayat barang rusak (API) berserta data transaksi terkait.
     * 
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        $items = DamagedItem::with(['transaction', 'inventory'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($d) {
                // Memformat response dengan mem-flatten data relasional
                return [
                    'id'              => $d->id,
                    'inventory_id'    => $d->inventory_id,
                    'item_code'       => $d->inventory?->code ?? '',
                    'item_name'       => $d->item_name,
                    'qty'             => $d->qty,
                    'description'     => $d->description,
                    'condition_notes' => $d->condition_notes,
                    'date'             => $d->created_at?->toISOString(),
                    'reported_by_name' => $d->reported_by_name,
                    'transaction_id'   => $d->transaction_id,
                    'transaction_code' => $d->transaction?->transaction_code,
                    'borrower_name'    => $d->transaction?->borrower_name,
                    'loan_date'        => $d->transaction?->loan_date?->toISOString(),
                    'damage_photo_url' => PhotoService::url($d->damage_photo),
                ];
            });

        return response()->json(['success' => true, 'data' => $items]);
    }

    /**
     * Mencatat laporan kerusakan barang baru dan mengurangi stok persediaan (inventory).
     * Dibungkus dengan Database Transaction untuk mencegah data tidak konsisten.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi payload request
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'qty'          => 'required|integer|min:1',
            'description'  => 'required|string',
            'damage_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        // Eksekusi DB transaction: Jika salah satu proses gagal, seluruh perubahan dibatalkan (rollback)
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            
            // lockForUpdate() digunakan untuk mencegah race condition (misalnya ada pengurangan stok yang terjadi bersamaan)
            $inv  = Inventory::lockForUpdate()->find($request->inventory_id);

            $qty = (int) $request->qty;
            
            // Proses kalkulasi pengurangang stok (baik total maupun available qty)
            $newTotal     = max(0, $inv->total_qty - $qty);
            $newAvailable = max(0, $inv->available_qty - $qty);

            $inv->update([
                'total_qty'     => $newTotal,
                'available_qty' => $newAvailable,
            ]);

            // Mencatat log barang rusak ke dalam tabel damaged_items
            $damaged = DamagedItem::create([
                'inventory_id'     => $inv->id,
                'item_name'        => $inv->name,
                'qty'              => $qty,
                'description'      => $request->description,
                'reported_by'      => $user->id,
                'reported_by_name' => $user->name,
                'damage_photo'     => $request->hasFile('damage_photo')
                                       ? PhotoService::store($request->file('damage_photo'), 'damage-photos')
                                       : null,
            ]);

            return response()->json(['success' => true, 'message' => 'Barang rusak berhasil dicatat.', 'data' => $damaged]);
        });
    }
}
