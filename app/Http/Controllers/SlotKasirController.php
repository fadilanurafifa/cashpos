<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\SlotKasir;
use Illuminate\Http\Request;

class SlotKasirController extends Controller
{
    public function index()
    {
        $kasirs = Kasir::all();
        return view('admin.slot_kasir.index', compact('kasirs'));
    }

    public function update(Request $request, $id)
    {
        $kasir = Kasir::findOrFail($id);
        $kasir->nama_kasir = $request->nama_kasir;
        $kasir->slot_kasir = $request->slot_kasir;
        $kasir->save();
    
        return redirect()->route('slot_kasir.index')->with('success', 'Data kasir berhasil diupdate.');
    }
    
    public function destroy($id)
    {
        $kasir = Kasir::findOrFail($id);
        $kasir->delete();
    
        return redirect()->route('slot_kasir.index')->with('success', 'Data kasir berhasil dihapus.');
    }
    
}


