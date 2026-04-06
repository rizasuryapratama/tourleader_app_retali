<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SesiAbsen;          // ⬅️ WAJIB
use App\Models\SesiAbsenItem;      // ⬅️ OPSIONAL (tapi rapi)

class SesiAbsenController extends Controller
{
    public function index()
    {
        $sesiAbsens = SesiAbsen::withCount('items')->latest()->get();
        return view('admin.jamaah.sesiabsen.index', compact('sesiAbsens'));
    }

    public function create()
    {
        return view('admin.jamaah.sesiabsen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|max:255',
        ]);

        $sesiAbsen = SesiAbsen::create([
            'judul' => $request->judul,
        ]);

        foreach ($request->items as $isi) {
            $sesiAbsen->items()->create([
                'isi' => $isi,
            ]);
        }

        return redirect()->route('admin.sesiabsen.index')
            ->with('success', 'Sesi absen berhasil dibuat');
    }

    // =========================
    // DETAIL
    // =========================
    public function show(SesiAbsen $sesiAbsen)
    {
        $sesiAbsen->load('items');
        return view('admin.jamaah.sesiabsen.show', compact('sesiAbsen'));
    }

    // =========================
    // EDIT
    // =========================
    public function edit(SesiAbsen $sesiAbsen)
    {
        $sesiAbsen->load('items');
        return view('admin.jamaah.sesiabsen.edit', compact('sesiAbsen'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, SesiAbsen $sesiAbsen)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.isi' => 'required|string|max:255',
        ]);

        $sesiAbsen->update([
            'judul' => $request->judul,
        ]);

        $existingIds = $sesiAbsen->items()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($request->items as $item) {

            if (isset($item['id'])) {
                // UPDATE existing
                SesiAbsenItem::where('id', $item['id'])
                    ->where('sesi_absen_id', $sesiAbsen->id)
                    ->update([
                        'isi' => $item['isi'],
                    ]);

                $submittedIds[] = $item['id'];
            } else {
                // CREATE new
                $new = $sesiAbsen->items()->create([
                    'isi' => $item['isi'],
                ]);

                $submittedIds[] = $new->id;
            }
        }

        // DELETE yang tidak dikirim
        $toDelete = array_diff($existingIds, $submittedIds);

        SesiAbsenItem::whereIn('id', $toDelete)->delete();

        return redirect()
            ->route('admin.sesiabsen.index')
            ->with('success', 'Sesi absen berhasil diperbarui');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy(SesiAbsen $sesiAbsen)
    {
        $sesiAbsen->delete();

        return redirect()->route('admin.sesiabsen.index')
            ->with('success', 'Sesi absen berhasil dihapus');
    }

    public function items(SesiAbsen $sesiAbsen)
    {
        return response()->json(
            $sesiAbsen->items()
                ->select('id', 'isi')
                ->orderBy('isi')
                ->get()
        );
    }
}
