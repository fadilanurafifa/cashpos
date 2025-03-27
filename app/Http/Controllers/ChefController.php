<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChefController extends Controller
{
    // Menampilkan halaman dashboard chef
    public function dashboard()
    {
        // Menghitung jumlah pesanan yang telah selesai
        $completedOrdersCount = Penjualan::where('status_pesanan', 'selesai')->count();

        // Mengambil menu terlaris berdasarkan jumlah pemesanan
        $topMenus = DetailPenjualan::select('produk_id', DB::raw('COUNT(*) as total'))
            ->groupBy('produk_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('produk')
            ->get();

        // Menghitung rata-rata waktu penyelesaian pesanan tanpa completed_at
        $averageCompletionTime = Penjualan::whereNotNull('created_at')
            ->where('status_pesanan', 'selesai')
            ->whereColumn('updated_at', '>', 'created_at') // Pastikan updated_at lebih besar dari created_at
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_time'))
            ->value('avg_time');

        return view('admin.chef.dashboard', compact('completedOrdersCount', 'topMenus', 'averageCompletionTime'));
    }

    // Menampilkan daftar pesanan untuk chef
    public function index()
    {
        $orders = Penjualan::where('status_pembayaran', 'lunas')
            ->whereIn('status_pesanan', ['pending', 'proses memasak'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chef.index', compact('orders'));
    }

    // Memperbarui status pesanan
    public function updateOrder(Request $request, $id)
    {
        $order = Penjualan::findOrFail($id);

        if ($request->status_pesanan == 'proses memasak') {
            $order->status_pesanan = 'proses memasak';
        } elseif ($request->status_pesanan == 'selesai') {
            $order->status_pesanan = 'selesai';
        }

        $order->save();

        return redirect()->back()->with('success', 'Status pesanan diperbarui.');
    }

    public function checkNewOrders()
    {
        $latestOrder = Penjualan::latest()->first(); // Ganti dengan logika pesanan masuk
        return response()->json([
            'message'   => $latestNotification->data['message'] ?? 'Pesanan baru masuk!',
            'order_id'  => $latestNotification->data['order_id'] ?? null,
            'status'    => $latestNotification->data['status'] ?? 'Diterima'
        ]);
    }
        public function readNotifications(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->unreadNotifications->markAsRead(); // Menandai semua sebagai telah dibaca
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
    
}
