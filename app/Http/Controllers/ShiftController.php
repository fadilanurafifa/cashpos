<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        return view('kasir.shift'); // View form input kasir
    }

    public function store(Request $request)
    {
        $request->validate([
            'slot' => 'required',
            'nama' => 'required|string|max:255',
        ]);
    
        session([
            'kasir_slot' => $request->slot,
            'kasir_nama' => $request->nama,
        ]);
    
        // Tambahkan ini untuk debug
        logger('Session Kasir:', session()->all());
    
        return redirect()->route('penjualan.index')->with('success', 'Shift dimulai sebagai Kasir ' . $request->slot);
    }
}
