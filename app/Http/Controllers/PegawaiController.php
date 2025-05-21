<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index()
    {
        $title = 'Delete Pegawai!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $pegawais = Pegawai::paginate(10);
        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        $lastId = Pegawai::max('id') ?? 0;
        $kode = 'PGW' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        
        return view('pegawai.create',compact('kode'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawais,nama',
            'kode' => 'required|string|max:255|unique:pegawais,kode',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);
        

        Pegawai::create($request->all());

        return redirect()->route('pegawai')->with('success', 'Pegawai created successfully.');
    }
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldPotonganNama = old('potongan_nama', []);  // Default produk_id null
        $oldPotonganJumlah = old('potongan_jumlah', []);  // Default jumlah 1
        $oldPotonganJenis = old('potongan_jenis', []);  // Default jumlah 1
        $oldTunjanganNama = old('tunjangan_nama', []);  // Default produk_id null
        $oldTunjanganJumlah = old('tunjangan_jumlah', []);  // Default jumlah 1

        $oldTunjangan = array_map(function ($tunjangan_nama, $tunjangan_jumlah) {
            return ['nama' => $tunjangan_nama, 'total' => $tunjangan_jumlah,'type' => 'tunjangan'];
        }, $oldTunjanganNama, $oldTunjanganJumlah);

        $oldPotongan = array_map(function ($potongan_nama, $potongan_jumlah,$potonganJenis) {
            return ['nama' => $potongan_nama, 'total' => $potongan_jumlah,'type' => $potonganJenis];
        }, $oldPotonganNama, $oldPotonganJumlah,$oldPotonganJenis);

        $old = array_merge($oldPotongan, $oldTunjangan);

        if (empty(old('potongan_nama')) 
            && empty(old('potongan_jumlah'))
            && empty(old('potongan_jenis'))
            && empty(old('tunjangan_nama'))
            && empty(old('tunjangan_jumlah'))
            ) {
            $old = [];
            foreach ($pegawai->penggajians as $penggajian) {
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

        return view('pegawai.edit', compact('pegawai','messages','old'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'potongan_nama'   => ['nullable', 'array', 'min:1'],
            'potongan_nama.*' => 'required|string|max:255',
            'potongan_jumlah'      => ['nullable', 'array'],
            'potongan_jumlah.*'    => 'required|numeric|min:1',
            'potongan_jenis'      => ['nullable', 'array'],
            'potongan_jenis.*'    => 'required|in:potongan_bulanan,potongan_absensi',
            'tunjangan_nama'   => ['nullable', 'array', 'min:1'],
            'tunjangan_nama.*' => 'required|string|max:255',
            'tunjangan_jumlah'      => ['nullable', 'array'],
            'tunjangan_jumlah.*'    => 'required|numeric|min:1',
        ]);

        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawais,nama,'.$id,
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->all());

        $pegawai->penggajians()->delete();

        $tunjangan = array_map(function ($nama, $jumlah) {
            return [
                'nama' => $nama,
                'total' => $jumlah,
                'type' => 'tunjangan',
            ];
        }, $request->tunjangan_nama ?? [], $request->tunjangan_jumlah ?? []);
        
        $potongan = array_map(function ($nama, $jumlah,$jenis) {
            return [
                'nama' => $nama,
                'total' => $jumlah,
                'type' => $jenis,
            ];
        }, $request->potongan_nama ?? [], $request->potongan_jumlah ?? [],$request->potongan_jenis ?? []);
        
        $dataPenggajian = array_merge($potongan, $tunjangan);
        
        // Simpan ulang penggajian
        $pegawai->penggajians()->createMany($dataPenggajian);        

        return back()->with('success', 'Pegawai updated successfully.');
    }
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai')->with('success', 'Pegawai deleted successfully.');
    }
}
