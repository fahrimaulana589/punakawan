<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

require __DIR__.'/auth.php';
