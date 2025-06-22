<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index()
    {
        $title = 'Delete Karyawan!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $karyawans = Karyawan::paginate(10);
        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        $lastId = Karyawan::max('id') ?? 0;
        $kode = 'KRY' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        
        return view('karyawan.create',compact('kode'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:karyawans,nama',
            'kode' => 'required|string|max:255|unique:karyawans,kode',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);
        

        $karyawan = Karyawan::create($request->all());

        return redirect()->route('karyawan.edit',$karyawan->id)->with('success', 'Karyawan created successfully.');
    }
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldPotonganNama = old('potongan_nama', []);  // Default produk_id null
        $oldPotonganJumlah = old('potongan_jumlah', []);  // Default jumlah 1
        $oldPotonganJenis = old('potongan_jenis', []);  // Default jumlah 1
        $oldTunjanganNama = old('tunjangan_nama', []);  // Default produk_id null
        $oldTunjanganJumlah = old('tunjangan_jumlah', []);  // Default jumlah 1
        $oldTunjanganJenis = old('tunjangan_jenis', []);  // Default jumlah 1
        
        $oldTunjangan = array_map(function ($tunjangan_nama, $tunjangan_jumlah,$tunjanganJenis) {
            return ['nama' => $tunjangan_nama, 'total' => $tunjangan_jumlah,'type' => $tunjanganJenis];
        }, $oldTunjanganNama, $oldTunjanganJumlah,$oldTunjanganJenis);

        $oldPotongan = array_map(function ($potongan_nama, $potongan_jumlah,$potonganJenis) {
            return ['nama' => $potongan_nama, 'total' => $potongan_jumlah,'type' => $potonganJenis];
        }, $oldPotonganNama, $oldPotonganJumlah,$oldPotonganJenis);

        $old = array_merge($oldPotongan, $oldTunjangan);

        if (empty(old('potongan_nama')) 
            && empty(old('potongan_jumlah'))
            && empty(old('potongan_jenis'))
            && empty(old('tunjangan_nama'))
            && empty(old('tunjangan_jumlah'))
            && empty(old('tunjangan_jenis'))
            ) {
            $old = [];
            foreach ($karyawan->penggajians as $penggajian) {
                $old[] = [
                    'nama' => $penggajian->nama,
                    'total' => $penggajian->total,
                    'type' => $penggajian->type
                ];
            }
        }

        $old = json_encode($old); // hasil asli: [{"produk_id":null,"jumlah":1}]

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages); // hasil asli: {"produk_ids":["The produk ids field is required."]}

        return view('karyawan.edit', compact('karyawan','messages','old'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'potongan_nama'   => ['nullable', 'array', 'min:1'],
            'potongan_nama.*' => 'required|string|max:255',
            'potongan_jumlah'      => ['nullable', 'array'],
            'potongan_jumlah.*'    => 'required|numeric|min:1|max:100000000',
            'potongan_jenis'      => ['nullable', 'array'],
            'potongan_jenis.*'    => 'required|in:potongan_bulanan,potongan_absensi',
            'tunjangan_nama'   => ['nullable', 'array', 'min:1'],
            'tunjangan_nama.*' => 'required|string|max:255',
            'tunjangan_jumlah'      => ['nullable', 'array'],
            'tunjangan_jumlah.*'    => 'required|numeric|min:1|max:100000000',
            'tunjangan_jenis'      => ['nullable', 'array'],
            'tunjangan_jenis.*'    => 'required|in:tunjangan_bulanan,tunjangan_harian',
        ]);

        $request->validate([
            'nama' => 'required|string|max:255|unique:karyawans,nama,'.$id,
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update($request->all());

        $karyawan->penggajians()->delete();

        $tunjangan = array_map(function ($nama, $jumlah,$jenis) {
            return [
                'nama' => $nama,
                'total' => $jumlah,
                'type' => $jenis,
            ];
        }, $request->tunjangan_nama ?? [], $request->tunjangan_jumlah ?? [],$request->tunjangan_jenis ?? []);
        
        $potongan = array_map(function ($nama, $jumlah,$jenis) {
            return [
                'nama' => $nama,
                'total' => $jumlah,
                'type' => $jenis,
            ];
        }, $request->potongan_nama ?? [], $request->potongan_jumlah ?? [],$request->potongan_jenis ?? []);
        
        $dataPenggajian = array_merge($potongan, $tunjangan);
        
        // Simpan ulang penggajian
        $karyawan->penggajians()->createMany($dataPenggajian);        

        return back()->with('success', 'Karyawan updated successfully.');
    }
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        try {
            $karyawan->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Karyawan deleted successfully.');
    }
}
