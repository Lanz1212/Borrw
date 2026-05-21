<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    public function index()
    {
        return view('borrowers.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Borrower::query();

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

    public function destroy(Borrower $borrower): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $borrower->delete();
        return response()->json(['success' => true, 'message' => 'Peminjam berhasil dihapus.']);
    }
}
