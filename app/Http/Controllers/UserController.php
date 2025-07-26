<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'jenis_instansi' => 'nullable|string|in:desa,pemda'
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
            'jenis_instansi' => $validated['jenis_instansi'],
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

    public function getAllUser(Request $request){
        $user_data = DB::table('users')->orderByRaw('email_verified_at IS NOT NULL')
        ->select('id', 'nama', 'email', 'instansi', 'email_verified_at', 'created_at')
        ->get();

        return response()->json($user_data);
    }

    public function updatePhotoProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.max' => 'File maksimal 2 MB',
            'photo.mimes' => 'Format gambar harus JPG atau PNG.',
            'photo.image' => 'File harus berupa gambar',
        ]);

        $profile_url = $user->profile_url;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Ubah nama file agar unik
            $filename = uniqid('profile_') . '.' . $file->getClientOriginalExtension();

            // Simpan file ke storage/app/public/profile_photo
            $file->storeAs('profile_photo', $filename, 'public');

            // hapus file lama
            if ($user->profile_url) {
                Storage::disk('public')->delete($user->profile_url);
            }

            // Simpan path baru
            $profile_url = 'profile_photo/' . $filename;
        }

        // Update kolom di database
        DB::table('users')->where('id', $user->id)->update([
            'profile_url' => $profile_url,
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
