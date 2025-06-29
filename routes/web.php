<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\KonsumsiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\PersediaanProdukJadiController;
use App\Http\Controllers\PersedianController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\BiayaController;
use App\Imports\AkunImport;
use App\Imports\DataImport;
use App\Models\Akun;
use Illuminate\Queue\Connectors\BeanstalkdConnector;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;
use iio\libmergepdf\Merger;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    // $html = view('welcome')->render();

    // $bodyHeight = Browsershot::html($html)
    //     ->setOption('width', '58mm')
    //     ->evaluate('document.body.scrollHeight');
    // // dd($bodyHeight);

    // // Generate PDF dan tampilkan langsung
    // $pdf1 = Browsershot::html($html)
    //     ->setOption('width', '58mm')
    //     ->setOption('height', '123px')
    //     ->pdf(); // return binary content

    // // Generate PDF dan tampilkan langsung
    // $pdf2 = Browsershot::html($html)
    //     ->setOption('width', '58mm')
    //     ->setOption('height', $bodyHeight . 'px')
    //     ->pdf(); // return binary content

    // // Gabungkan menggunakan libmergepdf
    // $merger = new Merger();
    // $merger->addRaw($pdf1);
    // $merger->addRaw($pdf2);

    // // Ambil hasil PDF gabungan
    // $mergedPdf = $merger->merge();

    // // Kirim ke browser
    // return Response::make($mergedPdf, 200, [
    //     'Content-Type' => 'application/pdf',
    //     'Content-Disposition' => 'inline; filename="gabungan.pdf"',
    // ]);

    return view('gaji.slip');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware('auth')->group(function () {
    // Karyawan (karyawan)
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan')->middleware('can:karyawan_read');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create')->middleware('can:karyawan_create');
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('karyawan.store')->middleware('can:karyawan_create');
    Route::get('/karyawan/edit/{id}', [KaryawanController::class, 'edit'])->name('karyawan.edit')->middleware('can:karyawan_edit');
    Route::put('/karyawan/update/{id}', [KaryawanController::class, 'update'])->name('karyawan.update')->middleware('can:karyawan_edit');
    Route::delete('/karyawan/delete/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.delete')->middleware('can:karyawan_delete');

});

Route::middleware('auth')->group(function () {
    // Produk
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk')->middleware('can:produk_read');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create')->middleware('can:produk_create');
    Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store')->middleware('can:produk_create');
    Route::get('/produk/edit/{id}', [ProdukController::class, 'edit'])->name('produk.edit')->middleware('can:produk_edit');
    // Route::get('/produk/paket/{id}', [ProdukController::class, 'paket'])->name('produk.paket')->middleware(['can:produk_edit']);
    // Route::post('/produk/paket/{id}/store', [ProdukController::class, 'storeToPaket'])->name('produk.paket.store')->middleware('can:produk_edit');
    Route::put('/produk/update/{id}', [ProdukController::class, 'update'])->name('produk.update')->middleware('can:produk_edit');
    Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.delete')->middleware('can:produk_delete');

});

Route::middleware('auth')->group(function () {
   // User
   Route::get('/user', [UserController::class, 'index'])->name('user')->middleware('can:user_read');
   Route::get('/user/create', [UserController::class, 'create'])->name('user.create')->middleware('can:user_create');
   Route::post('/user/store', [UserController::class, 'store'])->name('user.store')->middleware('can:user_create');
   Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit')->middleware('can:user_edit');
   Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update')->middleware('can:user_edit');
   Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete')->middleware('can:user_delete');

});

Route::middleware('auth')->group(function () {
     // Akun
     Route::get('/akun', [AkunController::class, 'index'])->name('akun')->middleware('can:akun_read');
     Route::get('/akun/create', [AkunController::class, 'create'])->name('akun.create')->middleware('can:akun_create');
     Route::post('/akun/store', [AkunController::class, 'store'])->name('akun.store')->middleware('can:akun_create');
     Route::get('/akun/edit/{id}', [AkunController::class, 'edit'])->name('akun.edit')->middleware('can:akun_edit');
     Route::put('/akun/update/{id}', [AkunController::class, 'update'])->name('akun.update')->middleware('can:akun_edit');
     Route::delete('/akun/delete/{id}', [AkunController::class, 'destroy'])->name('akun.delete')->middleware('can:akun_delete');
 
});

Route::middleware('auth')->group(function () {
    // Profile Perusahaan
    Route::get('/profile-perusahaan', [ProfileController::class, 'editProfile'])->name('profile.perusahaan')->middleware('can:profile_manage');
    Route::post('/profile-perusahaan/update', [ProfileController::class, 'updateProfile'])->name('profile.perusahaan.update')->middleware('can:profile_manage');
});


Route::middleware('auth')->group(function () {
   // Bahan Produksi
   Route::get('/bahanproduksi', [KonsumsiController::class, 'index'])->name('bahankonsumsi')->middleware('can:bahan_produksi_read');
   Route::get('/bahanproduksi/create', [KonsumsiController::class, 'create'])->name('bahankonsumsi.create')->middleware('can:bahan_produksi_create');
   Route::post('/bahanproduksi/store', [KonsumsiController::class, 'store'])->name('bahankonsumsi.store')->middleware('can:bahan_produksi_create');
   Route::get('/bahanproduksi/edit/{id}', [KonsumsiController::class, 'edit'])->name('bahankonsumsi.edit')->middleware('can:bahan_produksi_edit');
   Route::put('/bahanproduksi/update/{id}', [KonsumsiController::class, 'update'])->name('bahankonsumsi.update')->middleware('can:bahan_produksi_edit');
   Route::delete('/bahanproduksi/delete/{id}', [KonsumsiController::class, 'destroy'])->name('bahankonsumsi.delete')->middleware('can:bahan_produksi_delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('penjualan')->middleware(['permission:transaksi_kasir']); 
    Route::get('/transaksi/void', [TransaksiController::class, 'void'])->name('penjualan.void')->middleware(['permission:transaksi_kasir|transaksi_read']);
    Route::get('/transaksi/riwayat', [TransaksiController::class, 'riwayat'])->name('penjualan.riwayat')->middleware(['permission:transaksi_kasir|transaksi_read']);    
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('penjualan.create')->middleware(['can:transaksi_kasir']);
    Route::get('/transaksi/create/selesai', [TransaksiController::class, 'create'])->name('penjualan.create.manual')->middleware(['can:transaksi_create']);
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('penjualan.store')->middleware(['permission:transaksi_kasir|transaksi_create']);
    Route::get('/transaksi/show/{id}', [TransaksiController::class, 'show'])->name('penjualan.show')->middleware(['permission:transaksi_kasir|transaksi_read']);
    Route::get('/transaksi/struk/{id}', [TransaksiController::class, 'struk'])->name('penjualan.struk')->middleware(['permission:transaksi_kasir|transaksi_read']);
    Route::get('/transaksi/edit/{id}', [TransaksiController::class, 'edit'])->name('penjualan.edit')->middleware(['can:transaksi_edit']);
    Route::put('/transaksi/update/{id}', [TransaksiController::class, 'update'])->name('penjualan.update')->middleware(['can:transaksi_edit']);
    Route::post('/transaksi/cancel/{id}', [TransaksiController::class, 'cancel'])->name('penjualan.cancel')->middleware(['can:transaksi_kasir']);
    Route::post('/transaksi/finish/{id}', [TransaksiController::class, 'finish'])->name('penjualan.finish')->middleware(['can:transaksi_kasir']);    
    Route::post('/transaksi/delete/{id}', [TransaksiController::class, 'destroy'])->name('penjualan.destroy')->middleware(['can:transaksi_delete']);
});

Route::middleware('auth')->group(function () {
    Route::get('/belanja', [BelanjaController::class, 'index'])->name('belanja')->middleware('can:belanja_read');
    Route::get('/belanja/create', [BelanjaController::class, 'create'])->name('belanja.create')->middleware('can:belanja_create');
    Route::post('/belanja/store', [BelanjaController::class, 'store'])->name('belanja.store')->middleware('can:belanja_create');
    Route::get('/belanja/edit/{id}', [BelanjaController::class, 'edit'])->name('belanja.edit')->middleware('can:belanja_edit');
    Route::put('/belanja/update/{id}', [BelanjaController::class, 'update'])->name('belanja.update')->middleware('can:belanja_edit');
    Route::delete('/belanja/delete/{id}', [BelanjaController::class, 'destroy'])->name('belanja.delete')->middleware('can:belanja_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal')->middleware('can:jurnal_read');
    Route::get('/jurnal/create', [JurnalController::class, 'create'])->name('jurnal.create')->middleware('can:jurnal_create');
    Route::get('/jurnal/print', [JurnalController::class, 'print'])->name('jurnal.print')->middleware('can:jurnal_read');
    Route::post('/jurnal/store', [JurnalController::class, 'store'])->name('jurnal.store')->middleware('can:jurnal_create');
    Route::get('/jurnal/edit/{id}', [JurnalController::class, 'edit'])->name('jurnal.edit')->middleware('can:jurnal_edit');
    Route::put('/jurnal/update/{id}', [JurnalController::class, 'update'])->name('jurnal.update')->middleware('can:jurnal_edit');
    Route::delete('/jurnal/delete/{id}', [JurnalController::class, 'destroy'])->name('jurnal.delete')->middleware('can:jurnal_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/biaya', [BiayaController::class, 'index'])->name('biaya')->middleware('can:biaya_read');
    Route::get('/biaya/create', [BiayaController::class, 'create'])->name('biaya.create')->middleware('can:biaya_create');
    Route::post('/biaya/store', [BiayaController::class, 'store'])->name('biaya.store')->middleware('can:biaya_create');
    Route::get('/biaya/edit/{id}', [BiayaController::class, 'edit'])->name('biaya.edit')->middleware('can:biaya_edit');
    Route::put('/biaya/update/{id}', [BiayaController::class, 'update'])->name('biaya.update')->middleware('can:biaya_edit');
    Route::delete('/biaya/delete/{id}', [BiayaController::class, 'destroy'])->name('biaya.delete')->middleware('can:biaya_delete');

});


Route::middleware('auth')->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi')->middleware('can:absensi_read');
    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create')->middleware('can:absensi_create');
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store')->middleware('can:absensi_create');
    Route::get('/absensi/edit/{id}', [AbsensiController::class, 'edit'])->name('absensi.edit')->middleware('can:absensi_edit');
    Route::put('/absensi/update/{id}', [AbsensiController::class, 'update'])->name('absensi.update')->middleware('can:absensi_edit');
    Route::delete('/absensi/delete/{id}', [AbsensiController::class, 'destroy'])->name('absensi.delete')->middleware('can:absensi_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/persedian', [PersedianController::class, 'index'])->name('persedian')->middleware('can:persedian_read');
    Route::get('/persedian/create', [PersedianController::class, 'create'])->name('persedian.create')->middleware('can:persedian_create');
    Route::post('/persedian/store', [PersedianController::class, 'store'])->name('persedian.store')->middleware('can:persedian_create');
    Route::get('/persedian/edit/{id}', [PersedianController::class, 'edit'])->name('persedian.edit')->middleware('can:persedian_edit');
    Route::put('/persedian/update/{id}', [PersedianController::class, 'update'])->name('persedian.update')->middleware('can:persedian_edit');
    Route::delete('/persedian/delete/{id}', [PersedianController::class, 'destroy'])->name('persedian.delete')->middleware('can:persedian_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/persedianproduk', [PersediaanProdukJadiController::class, 'index'])->name('persedianproduk')->middleware('can:persedianproduk_read');
    Route::get('/persedianproduk/create', [PersediaanProdukJadiController::class, 'create'])->name('persedianproduk.create')->middleware('can:persedianproduk_create');
    Route::post('/persedianproduk/store', [PersediaanProdukJadiController::class, 'store'])->name('persedianproduk.store')->middleware('can:persedianproduk_create');
    Route::get('/persedianproduk/edit/{id}', [PersediaanProdukJadiController::class, 'edit'])->name('persedianproduk.edit')->middleware('can:persedianproduk_edit');
    Route::put('/persedianproduk/update/{id}', [PersediaanProdukJadiController::class, 'update'])->name('persedianproduk.update')->middleware('can:persedianproduk_edit');
    Route::delete('/persedianproduk/delete/{id}', [PersediaanProdukJadiController::class, 'destroy'])->name('persedianproduk.delete')->middleware('can:persedianproduk_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan')->middleware('can:laporan_read');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create')->middleware('can:laporan_create');
    Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store')->middleware('can:laporan_create');
    Route::get('/laporan/edit/{id}', [LaporanController::class, 'edit'])->name('laporan.edit')->middleware('can:laporan_edit');
    Route::put('/laporan/update/{id}', [LaporanController::class, 'update'])->name('laporan.update')->middleware('can:laporan_edit');
    Route::delete('/laporan/delete/{id}', [LaporanController::class, 'destroy'])->name('laporan.delete')->middleware('can:laporan_delete');
    // Route::get('/laporan/show/{id}', [LaporanController::class, 'show'])->name('laporan.show')->middleware('can:laporan_read');
    Route::get('/laporan/penjualan/{id}', [LaporanController::class, 'penjualan'])->name('laporan.penjualan')->middleware('can:laporan_penjualan');
    Route::get('/laporan/jurnal/{id}', [LaporanController::class, 'jurnal'])->name('laporan.jurnal')->middleware('can:laporan_jurnal');
    Route::get('/laporan/bukubesar/{id}', [LaporanController::class, 'bukuBesar'])->name('laporan.bukubesar')->middleware('can:buku_besar');
    Route::get('/laporan/neracasaldo/{id}', [LaporanController::class, 'neracaSaldo'])->name('laporan.neracasaldo')->middleware('can:neraca_saldo');
    Route::get('/laporan/ajp/{id}', [LaporanController::class, 'ajp'])->name('laporan.ajp')->middleware('can:ajp');
    Route::get('/laporan/neracalajur/{id}', [LaporanController::class, 'neracaLajur'])->name('laporan.neracalajur')->middleware('can:neraca_lajur');
    Route::get('/laporan/labarugi/{id}', [LaporanController::class, 'labaRugi'])->name('laporan.labarugi')->middleware('can:laba_rugi');
    Route::get('/laporan/posisikeuangan/{id}', [LaporanController::class, 'posisiKeuangan'])->name('laporan.posisikeuangan')->middleware('can:posisi_keuangan');
    Route::get('/laporan/perubahanmodal/{id}', [LaporanController::class, 'perubahanModal'])->name('laporan.perubahanmodal')->middleware('can:perubahan_modal');
    Route::get('/laporan/hpp/{id}', [LaporanController::class, 'hpp'])->name('laporan.hpp')->middleware('can:hpp');
    Route::get('/laporanlist/{laporan}/', [LaporanController::class, 'laporanList'])->name('laporan.list');
    Route::get('/laporan/rekap_penjualan', [LaporanController::class, 'rekap'])->name('laporan.rekap')->middleware(['can:rekap_penjualan']);
    Route::get('/laporan/rekap_penjualan/print', [LaporanController::class, 'rekap_print'])->name('laporan.rekap_print')->middleware(['can:rekap_penjualan']);

});

Route::middleware('auth')->group(function () {
    Route::get('/peralatan', [PeralatanController::class, 'index'])->name('peralatan')->middleware('can:peralatan_read');
    Route::get('/peralatan/create', [PeralatanController::class, 'create'])->name('peralatan.create')->middleware('can:peralatan_create');
    Route::post('/peralatan/store', [PeralatanController::class, 'store'])->name('peralatan.store')->middleware('can:peralatan_create');
    Route::get('/peralatan/edit/{id}', [PeralatanController::class, 'edit'])->name('peralatan.edit')->middleware('can:peralatan_edit');
    Route::put('/peralatan/update/{id}', [PeralatanController::class, 'update'])->name('peralatan.update')->middleware('can:peralatan_edit');
    Route::delete('/peralatan/delete/{id}', [PeralatanController::class, 'destroy'])->name('peralatan.delete')->middleware('can:peralatan_delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji')->middleware('can:gaji_read');
    Route::get('/gaji/create', [GajiController::class, 'create'])->name('gaji.create')->middleware('can:gaji_create');
    Route::get('/gaji/generate', [GajiController::class, 'generate'])->name('gaji.generate')->middleware('can:gaji_create');
    Route::post('/gaji/store', [GajiController::class, 'store'])->name('gaji.store')->middleware('can:gaji_create');
    Route::post('/gaji/store/generate', [GajiController::class, 'storeGenerate'])->name('gaji.store.generate')->middleware('can:gaji_create');
    Route::get('/gaji/edit/{gaji}', [GajiController::class, 'edit'])->name('gaji.edit')->middleware('can:gaji_edit');
    Route::get('/gaji/show/{gaji}', [GajiController::class, 'show'])->name('gaji.show')->middleware('can:gaji_read');
    Route::put('/gaji/update/{gaji}', [GajiController::class, 'update'])->name('gaji.update')->middleware('can:gaji_edit');
    Route::delete('/gaji/delete/{gaji}', [GajiController::class, 'destroy'])->name('gaji.delete')->middleware('can:gaji_delete');
    Route::get('/gaji/slip/{gaji}', [GajiController::class, 'slip'])->name('gaji.slip')->middleware('can:gaji_read');
});

require __DIR__.'/auth.php';
