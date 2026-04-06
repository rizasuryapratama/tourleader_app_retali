<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Muthawif;
use App\Models\Kloter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MuthawifController extends Controller
{
    public function index()
    {
        $muthawifs = Muthawif::with('kloter')->latest()->get();
        return view('admin.muthawif.index', compact('muthawifs'));
    }

    public function create()
    {
        $kloters = Kloter::all();
        return view('admin.muthawif.create', compact('kloters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:muthawifs,email',
            'password' => 'required|string|min:8', // Added password validation
            'kloter_id' => 'required|exists:kloters,id',
        ]);

        // Hash the password before saving
        $validated['password'] = Hash::make($validated['password']);

        Muthawif::create($validated);

        return redirect()->route('muthawif.index')
            ->with('success', 'Muthawif berhasil ditambahkan!');
    }

    public function edit(Muthawif $muthawif)
    {
        $kloters = Kloter::all();
        return view('admin.muthawif.edit', compact('muthawif', 'kloters'));
    }

    public function update(Request $request, Muthawif $muthawif)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:muthawifs,email,' . $muthawif->id,
            'kloter_id' => 'required|exists:kloters,id',
            'password' => 'nullable|string|min:8', // Make password optional for update
        ]);

        // Hash the password if it's provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Remove password from the update data if not provided
        }

        $muthawif->update($validated);

        return redirect()->route('muthawif.index')
            ->with('success', 'Muthawif berhasil diupdate!');
    }

    public function destroy(Muthawif $muthawif)
    {
        $muthawif->delete();

        return redirect()->route('muthawif.index')
            ->with('success', 'Muthawif berhasil dihapus!');
    }
}
