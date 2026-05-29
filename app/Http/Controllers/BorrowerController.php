<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller BorrowerController
 * 
 * Mengelola data peminjam (borrower) yang mencakup operasi CRUD.
 */
class BorrowerController extends Controller
{
    /**
     * Menampilkan halaman daftar peminjam.
     */
    public function index()
    {
        return view('borrowers.index');
    }

    /**
     * Mengambil data peminjam (API) dengan dukungan pencarian (search).
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $query = Borrower::query();

        // Fitur pencarian berdasarkan nama, departemen, atau kontak
        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        $borrowers = $query->orderBy('name')->get();

        return response()->json(['success' => true, 'data' => $borrowers]);
    }

    /**
     * Menyimpan data peminjam baru ke database.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'contact'    => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $borrower = Borrower::create($validated);

        return response()->json(['success' => true, 'message' => 'Peminjam berhasil ditambahkan.', 'data' => $borrower]);
    }

    /**
     * Memperbarui data peminjam yang sudah ada.
     * 
     * @param Request $request
     * @param Borrower $borrower Data peminjam yang akan diupdate (Route Model Binding)
     * @return JsonResponse
     */
    public function update(Request $request, Borrower $borrower): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'contact'    => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $borrower->update($validated);

        return response()->json(['success' => true, 'message' => 'Peminjam berhasil diupdate.', 'data' => $borrower->fresh()]);
    }

    /**
     * Menghapus data peminjam. 
     * Hanya admin yang diizinkan untuk melakukan aksi ini.
     * 
     * @param Borrower $borrower
     * @return JsonResponse
     */
    public function destroy(Borrower $borrower): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $borrower->delete();
        return response()->json(['success' => true, 'message' => 'Peminjam berhasil dihapus.']);
    }
}
