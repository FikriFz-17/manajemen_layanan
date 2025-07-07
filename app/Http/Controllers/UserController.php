<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'instansi' => 'required|string',
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'phone.required' => 'No.Hp tidak boleh kosong',
            'instansi.required' => 'Instansi tidak boleh kosong',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'instansi' => $validated['instansi'],
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

    public function getAllUser(Request $request){
        $user_data = DB::table('users')->orderByRaw('email_verified_at IS NOT NULL')->get();

        return response()->json($user_data);
    }
}
