<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\KonsumsiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Queue\Connectors\BeanstalkdConnector;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
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
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai');

    Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    
    Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
    
    Route::get('/pegawai/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawai.edit');

    Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');

    Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk');

    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    
    Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
    
    Route::get('/produk/edit/{id}', [ProdukController::class, 'edit'])->name('produk.edit');

    Route::get('/produk/paket/{id}', [ProdukController::class, 'paket'])->name('produk.paket')->middleware('filter.produk.induk');

    Route::post('/produk/paket/{id}/store', [ProdukController::class, 'storeToPaket'])->name('produk.paket.store');

    Route::put('/produk/update/{id}', [ProdukController::class, 'update'])->name('produk.update');

    Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user');

    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');

    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');

    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/akun', [AkunController::class, 'index'])->name('akun');

    Route::get('/akun/create', [AkunController::class, 'create'])->name('akun.create');
    
    Route::post('/akun/store', [AkunController::class, 'store'])->name('akun.store');
    
    Route::get('/akun/edit/{id}', [AkunController::class, 'edit'])->name('akun.edit');

    Route::put('/akun/update/{id}', [AkunController::class, 'update'])->name('akun.update');

    Route::delete('/akun/delete/{id}', [AkunController::class, 'destroy'])->name('akun.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/bahankonsumsi', [KonsumsiController::class, 'index'])->name('bahankonsumsi');

    Route::get('/bahankonsumsi/create', [KonsumsiController::class, 'create'])->name('bahankonsumsi.create');
    
    Route::post('/bahankonsumsi/store', [KonsumsiController::class, 'store'])->name('bahankonsumsi.store');
    
    Route::get('/bahankonsumsi/edit/{id}', [KonsumsiController::class, 'edit'])->name('bahankonsumsi.edit');

    Route::put('/bahankonsumsi/update/{id}', [KonsumsiController::class, 'update'])->name('bahankonsumsi.update');

    Route::delete('/bahankonsumsi/delete/{id}', [KonsumsiController::class, 'destroy'])->name('bahankonsumsi.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/penjualan', [TransaksiController::class, 'index'])->name('penjualan');
    
    Route::get('/penjualan/void', [TransaksiController::class, 'void'])->name('penjualan.void');

    Route::get('/penjualan/riwayat', [TransaksiController::class, 'riwayat'])->name('penjualan.riwayat');    

    Route::get('/penjualan/create', [TransaksiController::class, 'create'])->name('penjualan.create');

    Route::post('/penjualan/store', [TransaksiController::class, 'store'])->name('penjualan.store');
    
    Route::get('/penjualan/show/{id}', [TransaksiController::class, 'show'])->name('penjualan.show');

    Route::post('/penjualan/cancel/{id}', [TransaksiController::class, 'cancel'])->name('penjualan.cancel');

    Route::post('/penjualan/finish/{id}', [TransaksiController::class, 'finish'])->name('penjualan.finish');
    
    Route::post('/penjualan/hapus/{id}', [TransaksiController::class, 'destroy'])->name('penjualan.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/belanja', [BelanjaController::class, 'index'])->name('belanja');
    
    Route::get('/belanja/create', [BelanjaController::class, 'create'])->name('belanja.create');

    Route::post('/belanja/store', [BelanjaController::class, 'store'])->name('belanja.store');
    
    Route::get('/belanja/edit/{id}', [BelanjaController::class, 'edit'])->name('belanja.edit');

    Route::put('/belanja/update/{id}', [BelanjaController::class, 'update'])->name('belanja.update');

    Route::delete('/belanja/delete/{id}', [BelanjaController::class, 'destroy'])->name('belanja.delete');

});

Route::middleware('auth')->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');

    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    
    Route::get('/absensi/edit/{id}', [AbsensiController::class, 'edit'])->name('absensi.edit');

    Route::put('/absensi/update/{id}', [AbsensiController::class, 'update'])->name('absensi.update');

    Route::delete('/absensi/delete/{id}', [AbsensiController::class, 'destroy'])->name('absensi.delete');
});

require __DIR__.'/auth.php';
