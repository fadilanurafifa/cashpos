<?php

namespace App\Http\Controllers; // Namespace controller berada di folder utama Controllers

use App\Models\DetailPenjualan; // Mengimpor model DetailPenjualan
use App\Models\Penjualan; // Mengimpor model Penjualan
use Illuminate\Http\Request; // Untuk menangani request HTTP
use Illuminate\Support\Facades\Auth; // Untuk mengakses data user yang login
use Illuminate\Support\Facades\DB; // Untuk menjalankan query database mentah

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
    
        // Hitung rata-rata waktu penyelesaian pesanan dalam menit
        $averageCompletionTime = Penjualan::whereNotNull('created_at')
            ->where('status_pesanan', 'selesai')
            ->whereColumn('updated_at', '>', 'created_at') // Hanya data dengan updated_at > created_at
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_time')) // Rata-rata selisih waktu
            ->value('avg_time'); // Mengambil nilai avg_time langsung
    
        // Tampilkan data ke view dashboard chef
        return view('admin.chef.dashboard', compact(
            'completedOrdersCount', 'topMenus', 'menuNames', 'menuOrders', 'averageCompletionTime'
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

        return redirect()->back()->with('success', 'Status pesanan diperbarui.'); // Redirect dengan pesan sukses
    }

    // Mengecek pesanan terbaru secara berkala (digunakan untuk notifikasi real-time)
    public function checkNewOrders()
    {
        $latestOrder = Penjualan::latest()->first(); // Ambil pesanan terakhir yang masuk

        // ❗️Catatan: Variabel $latestNotification tidak ada, seharusnya pakai $latestOrder langsung
        return response()->json([
            'message'   => $latestNotification->data['message'] ?? 'Pesanan baru masuk!', // Pesan default
            'order_id'  => $latestNotification->data['order_id'] ?? null, // ID pesanan
            'status'    => $latestNotification->data['status'] ?? 'Diterima' // Status default
        ]);
    }

    // Menandai semua notifikasi sebagai telah dibaca
    public function readNotifications(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login

        if ($user) {
            $user->unreadNotifications->markAsRead(); // Tandai semua notifikasi sebagai telah dibaca
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.'); // Kembali dengan pesan sukses
    }
}
