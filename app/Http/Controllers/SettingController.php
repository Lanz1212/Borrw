<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function data(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'app_name'     => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'categories'   => 'nullable|string',
        ]);

        foreach (['app_name', 'company_name', 'categories'] as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
    }
}
