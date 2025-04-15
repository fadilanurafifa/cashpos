<?php

namespace App\Http\Controllers; // Namespace controller berada di folder utama Controllers

use App\Models\DetailPenjualan; // Mengimpor model DetailPenjualan
use App\Models\Penjualan; // Mengimpor model Penjualan
use Illuminate\Http\Request; // Untuk menangani request HTTP
use Illuminate\Support\Facades\Auth; // Untuk mengakses data user yang login
use Illuminate\Support\Facades\DB; // Untuk menjalankan query database mentah
use Illuminate\Support\Facades\Log;

class ChefController extends Controller // Controller khusus untuk chef
{
    // Menampilkan halaman dashboard chef
    public function dashboard()
    {
        // Hitung jumlah pesanan dengan status "selesai"
        $completedOrdersCount = Penjualan::where('status_pesanan', 'selesai')->count();
    
        // Ambil 5 menu terlaris berdasarkan jumlah pemesanan
        $topMenus = DetailPenjualan::select('produk_id', DB::raw('COUNT(*) as total'))
            ->groupBy('produk_id') // Mengelompokkan berdasarkan produk_id
            ->orderByDesc('total') // Mengurutkan dari yang terbanyak
            ->take(5) // Ambil 5 teratas
            ->with('produk') // Memuat relasi ke model Produk
            ->get();
    
        // Mengambil nama produk dari relasi untuk digunakan di chart
        $menuNames = $topMenus->pluck('produk.nama_produk'); // Ambil nama produk dari relasi produk
        $menuOrders = $topMenus->pluck('total'); // Ambil total pemesanan dari hasil query
        
        // Hitung jumlah pesanan dengan status selain "selesai" (masih aktif/diproses)
        $activeOrdersCount = Penjualan::where('status_pesanan', '!=', 'selesai')->count();

        Log::debug('Data dashboard chef:', [
            'completedOrdersCount' => $completedOrdersCount,
            'topMenus' => $topMenus->toArray(),
            'activeOrdersCount' => $activeOrdersCount,
        ]);
    
        // Tampilkan data ke view dashboard chef
        return view('admin.chef.dashboard', compact(
            'completedOrdersCount', 'topMenus', 'menuNames', 'menuOrders', 'activeOrdersCount'
        ));
    }

    // Menampilkan daftar pesanan untuk chef
    public function index()
    {
        // Ambil pesanan yang sudah dibayar dan status pending atau sedang dimasak
        $orders = Penjualan::where('status_pembayaran', 'lunas')
            ->whereIn('status_pesanan', ['pending', 'proses memasak']) // Filter status pesanan
            ->orderBy('created_at', 'asc') // Urutkan berdasarkan waktu masuk
            ->get(); // Ambil semua data

            Log::debug('Pesanan aktif untuk chef:', [
                'jumlah_pesanan' => $orders->count(),
                'orders' => $orders->toArray(),
            ]);

        return view('admin.chef.index', compact('orders')); // Kirim ke view index chef
    }

    // Memperbarui status pesanan oleh chef
    public function updateOrder(Request $request, $id)
    {
        $order = Penjualan::findOrFail($id); // Cari pesanan berdasarkan ID

        if ($request->status_pesanan == 'proses memasak') {
            $order->status_pesanan = 'proses memasak'; // Ubah status jadi memasak
        } elseif ($request->status_pesanan == 'selesai') {
            $order->status_pesanan = 'selesai'; // Ubah status jadi selesai
        }

        $order->save(); // Simpan perubahan ke database

        Log::info('Status pesanan diperbarui', [
            'order_id' => $order->id,
            'new_status' => $order->status_pesanan,
            'updated_by' => Auth::user()->name ?? 'Unknown',
        ]);    

        return redirect()->back()->with('success', 'Status pesanan diperbarui.'); // Redirect dengan pesan sukses
    }

    // Mengecek pesanan terbaru secara berkala (digunakan untuk notifikasi real-time)
    // public function checkNewOrders()
    // {
    //     $latestOrder = Penjualan::latest()->first(); // Ambil pesanan terakhir yang masuk

    //     // Catatan: Variabel $latestNotification tidak ada, seharusnya pakai $latestOrder langsung
    //     return response()->json([
    //         'message'   => $latestNotification->data['message'] ?? 'Pesanan baru masuk!', // Pesan default
    //         'order_id'  => $latestNotification->data['order_id'] ?? null, // ID pesanan
    //         'status'    => $latestNotification->data['status'] ?? 'Diterima' // Status default
    //     ]);
    // }

    public function checkNewOrders()
    {
        $latestOrder = Penjualan::latest()->first();

        if ($latestOrder) {
            Log::debug('Cek pesanan terbaru', [
                'order_id' => $latestOrder->id,
                'status' => $latestOrder->status_pesanan,
            ]);
        } else {
            Log::debug('Tidak ada pesanan terbaru ditemukan');
        }

        return response()->json([
            'message'   => 'Pesanan baru masuk!',
            'order_id'  => $latestOrder->id ?? null,
            'status'    => $latestOrder->status_pesanan ?? 'Diterima'
        ]);
    }


    // Menandai semua notifikasi sebagai telah dibaca
    public function readNotifications(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login

        if ($user) {
            $user->unreadNotifications->markAsRead(); // Tandai semua notifikasi sebagai telah dibaca
            $unreadCount = $user->unreadNotifications->count();

            Log::info('Notifikasi ditandai sebagai telah dibaca', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'jumlah_notifikasi' => $unreadCount
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.'); // Kembali dengan pesan sukses
    }
}
