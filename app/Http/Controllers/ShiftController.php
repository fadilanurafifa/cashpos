<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    public function index()
    {
        $daftarKasir = Kasir::all();
        return view('kasir.shift', compact('daftarKasir'));
    }

    public function store(Request $request)
    {
      
        $request->validate([
            'slot' => 'required',
            'kasir_id' => 'nullable',
            'nama_baru' => 'required_if:kasir_id,lainnya'
        ]);

        $kasirId = null;

        if ($request->kasir_id === 'lainnya') {
            $kasirBaru = Kasir::create([
                'nama_kasir' => $request->nama_baru,
                'slot_kasir' => $request->slot,
            ]);
            $kasirId = $kasirBaru->id;
            Log::info("Kasir baru dibuat dengan ID: $kasirId");
        } else {
            $kasir = Kasir::find($request->kasir_id);
            if (!$kasir) {
                Log::error("Kasir ID {$request->kasir_id} tidak ditemukan.");
                return back()->withErrors(['kasir_id' => 'Kasir tidak ditemukan.']);
            }
            $kasirId = $kasir->id;
            Log::info("Kasir lama dipilih, ID: $kasirId");
        }

        session([
            'kasir_id' => $kasirId,
        ]);
        Log::info("Session kasir_id diset: " . session('kasir_id'));

        return redirect()->route('penjualan.index')->with('success', 'Shift dimulai');
    }
}
