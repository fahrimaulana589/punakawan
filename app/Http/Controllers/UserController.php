<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
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
        $pegawais = Pegawai::doesntHave('user')->get();
        return view('user.create', compact('pegawais'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['Kasir', 'Direktur SDM','Direktur Produksi','Direktur Keuangan'])],
            'pegawai_id' => 'required|string|max:255|unique:users,pegawai_id',
        ]);

        $user = User::create($request->all());
        $user->assignRole($request->role);

        return redirect()->route('user')->with('success', 'Pegawai created successfully.');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $pegawai = $user->pegawai;

        $pegawais = Pegawai::doesntHave('user')->get();
        
        if ($pegawai) {
            $pegawais->push($pegawai);
        }

        return view('user.edit', compact('user', 'pegawais'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,'.$id,
            'email' => 'required|string|max:255|unique:users,email,'.$id,
            'password' => 'string|max:255|nullable',
            'role' => ['required', 'string', Rule::in(['Kasir', 'Direktur SDM','Direktur Produksi','Direktur Keuangan'])],
            'pegawai_id' => 'required|string|max:255|unique:users,pegawai_id,'.$id,
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

        return redirect()->route('user')->with('success', 'User deleted successfully.');
    }
}
