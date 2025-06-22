<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $title = 'Delete User !';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $users = User::paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $karyawans = Karyawan::doesntHave('user')->get();
        return view('user.create', compact('karyawans'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['Kasir', 'Direktur SDM','Direktur Produksi','Direktur Keuangan'])],
            'karyawan_id' => 'required|string|max:255|unique:users,karyawan_id',
        ]);

        $user = User::create($request->all());
        $user->assignRole($request->role);

        return redirect()->route('user')->with('success', 'Karyawan created successfully.');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $karyawan = $user->karyawan;

        $karyawans = Karyawan::doesntHave('user')->get();
        
        if ($karyawan) {
            $karyawans->push($karyawan);
        }

        return view('user.edit', compact('user', 'karyawans'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,'.$id,
            'email' => 'required|string|max:255|unique:users,email,'.$id,
            'password' => 'string|max:255|nullable',
            'role' => ['required', 'string', Rule::in(['Kasir', 'Bagian SDM','Bagian Produksi','Bagian Keuangan'])],
            'karyawan_id' => 'required|string|max:255|unique:users,karyawan_id,'.$id,
        ]);

        $user = User::findOrFail($id);

        if($request->get('password') == null){
            $request->request->remove('password');
        }

        $user->update($request->all());

        // Hapus role lama dan tetapkan role baru
        $user->syncRoles([$request->role]);

        return back()->with('success', 'User updated successfully.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        try {
            $user->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'User deleted successfully.');
    }
}
