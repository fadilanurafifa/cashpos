<?php

use App\Exports\ProdukExport;
use App\Http\Controllers\AbsensiKerjaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\HistoryPenjualanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanProdukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengajuanBarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SlotKasirController;
use App\Http\Controllers\TransaksiController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\PengajuanBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::post('/kasir/read-notifications', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('kasir.readNotifications');


// admin
Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('role:admin');

// kasir
Route::middleware([RoleMiddleware::class . ':kasir'])->group(function () {
    Route::get('/kasir', function () {
        return view('kasir.index');
    })->name('kasir.dashboard');
});

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('admin.login');
});

Route::group(['middleware' => ['role:admin,owner,chef']], function () {
Route::get('/dashboard', [DashboardController::class, 'indexPage'])->name('dashboard');
});

// login
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');
// Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

// kategori
Route::get('admin/kategori', [KategoriController::class, 'index'])->name('kategori.index');
Route::post('admin/kategori', [KategoriController::class, 'store'])->name('kategori.store');
Route::delete('/admin/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
Route::put('/admin/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
Route::get('/admin/kategori/export-excel', [KategoriController::class, 'exportExcel'])->name('kategori.exportExcel');
Route::get('/admin/kategori/export-pdf', [KategoriController::class, 'exportPDF'])->name('kategori.exportPDF');

Route::post('/import-kategori', [KategoriController::class, 'import'])->name('kategori.import');  

// Route::group(['middleware' => ['role:admin']], function () {
// produk
Route::prefix('admin')->group(function () {
    Route::resource('produk', ProdukController::class)->names([
        'index' => 'admin.produk.index',
        'create' => 'admin.produk.create',
        'store' => 'admin.produk.store',
        'edit' => 'admin.produk.edit',
        'destroy' => 'admin.produk.destroy',
    ]);
    Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    Route::post('/produk/upload-gambar', [ProdukController::class, 'uploadGambar'])->name('produk.uploadGambar');

    // Rute khusus untuk memperbarui stok saja
    Route::put('produk/{produk}/update-stok', [ProdukController::class, 'updateStok'])
        ->name('admin.produk.update-stok');
});
    
    // penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');

Route::group(['middleware' => ['role:kasir,admin']], function () {
    // pelanggan 
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::get('/pelanggan/{id}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::get('/pelanggan/export-excel', [PelangganController::class, 'exportExcel'])->name('pelanggan.exportExcel');
    Route::get('/pelanggan/export-pdf', [PelangganController::class, 'exportPdf'])->name('pelanggan.exportPdf'); 
    Route::post('/import-pelanggan', [PelangganController::class, 'import'])->name('pelanggan.import');   

    // pembayaran 
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/{no_faktur}', [PembayaranController::class, 'show'])->name('show');
            Route::post('/bayar/{no_faktur}', [PembayaranController::class, 'bayar'])->name('bayar');
            Route::get('/print/{no_faktur}', [PembayaranController::class, 'print'])->name('print');
        });
    }); 
});

Route::group(['middleware' => ['role:owner,admin']], function () {
// history Transaksi
Route::get('/admin/history-penjualan', [HistoryPenjualanController::class, 'index'])->name('history.penjualan');
Route::get('/cetak-struk/{id}', [HistoryPenjualanController::class, 'cetakStruk'])->name('cetak.struk');
Route::get('/export/transaksi/excel', [HistoryPenjualanController::class, 'exportExcel'])->name('export.transaksi.excel');
Route::get('/export/transaksi/pdf', [HistoryPenjualanController::class, 'exportPDF'])->name('export.transaksi.pdf');


// laporan penjualan
Route::get('/admin/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan.penjualan');
Route::get('/admin/laporan/cetak-pdf', [LaporanPenjualanController::class, 'cetakPDF'])->name('admin.laporan.cetak');
Route::get('/laporan-transaksi/export-pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('laporan.transaksi.pdf');
Route::get('/cetak-laporan', [LaporanPenjualanController::class, 'cetakPDF'])->name('cetak.pdf');
Route::get('/admin/laporan/penjualan/excel', [LaporanPenjualanController::class, 'exportExcel'])->name('admin.laporan.exportExcel');


// laporan produk
Route::get('/laporan-produk', [LaporanProdukController::class, 'laporanProduk'])->name('laporan.produk');
Route::get('/laporan-produk/cetak', [LaporanProdukController::class, 'cetakLaporanProduk'])->name('laporan.produk.pdf');
});
Route::get('/laporan/produk/excel', [LaporanProdukController::class, 'exportExcel'])->name('laporan.produk.excel');

// halaman chef
Route::get('/chef/dashboard', [ChefController::class, 'dashboard'])->name('chef.dashboard');
Route::get('/chef/orders', [ChefController::class, 'index'])->name('chef.index');
Route::put('/chef/update-order/{id}', [ChefController::class, 'updateOrder'])->name('chef.updateOrder');
Route::get('/chef/checkNewOrders', [ChefController::class, 'checkNewOrders'])->name('chef.checkNewOrders');
Route::post('/chef/read-notifications', [ChefController::class, 'readNotifications'])->name('chef.readNotifications');

// pengajuan
Route::get('/pengajuan', [PengajuanBarangController::class, 'index'])->name('admin.pengajuan.index');
Route::post('/pengajuan', [PengajuanBarangController::class, 'store'])->name('admin.pengajuan.store');
Route::put('/pengajuan/{id}', [PengajuanBarangController::class, 'update'])->name('admin.pengajuan.update');
Route::delete('/pengajuan/{id}', [PengajuanBarangController::class, 'destroy'])->name('admin.pengajuan.destroy');
Route::put('/pengajuan/updateStatus/{id}', [PengajuanBarangController::class, 'updateStatus'])->name('admin.pengajuan.updateStatus');
Route::get('/pengajuan/export/excel', [PengajuanBarangController::class, 'exportExcel'])->name('pengajuan.export.excel');
Route::get('/pengajuan/export/pdf', [PengajuanBarangController::class, 'exportPDF'])->name('pengajuan.export.pdf');


// Buat route shift di luar group middleware auth
Route::get('/shift', [ShiftController::class, 'index'])->name('kasir.shift');
Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');

// Setelah login, baru yang ini pake middleware:
Route::middleware(['web', 'auth', 'role:kasir,admin'])->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    // ...
});

// kasir
Route::get('/slot_kasir', [SlotKasirController::class, 'index'])->name('slot_kasir.index');
Route::post('/slot_kasir/update/{id}', [SlotKasirController::class, 'update'])->name('slot_kasir.update');
Route::delete('/slot_kasir/delete/{id}', [SlotKasirController::class, 'destroy'])->name('slot_kasir.delete');

// absensi kerja
Route::get('/absensi', [AbsensiKerjaController::class, 'index'])->name('absensi.index');
Route::post('/absensi', [AbsensiKerjaController::class, 'store'])->name('absensi.store');
Route::post('/absensi/{id}/selesai', [AbsensiKerjaController::class, 'selesaiKerja'])->name('absensi.selesai');
Route::delete('/absensi/{id}', [AbsensiKerjaController::class, 'destroy'])->name('absensi.destroy');
Route::put('/absensi/{id}/update-data', [AbsensiKerjaController::class, 'updateDataAbsensi'])->name('absensi.update.data');
Route::put('/absensi/{id}/update-status', [AbsensiKerjaController::class, 'updateStatus'])->name('absensi.update.status');

// export absensi kerja
Route::get('/absensi/export/excel', [AbsensiKerjaController::class, 'exportExcel'])->name('absensi.export.excel');
Route::get('/absensi/export-pdf', [AbsensiKerjaController::class, 'exportPDF'])->name('absensi.export.pdf');
// Route untuk import data absensi
Route::post('/absensi/import', [AbsensiKerjaController::class, 'import'])->name('absensi.import');


// update status langsung pada table
Route::put('/absensi/{id}', [AbsensiKerjaController::class, 'updateStatus'])->name('absensi.updateStatus');




