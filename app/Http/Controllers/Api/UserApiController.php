<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    public function getAllUser(){
        $user_data = DB::table('users')->orderByRaw('email_verified_at IS NOT NULL')
        ->select('id', 'nama', 'email', 'instansi', 'email_verified_at', 'created_at', 'profile_url')
        ->get()
        ->map(function ($item){
            $item->profile_url = $item->profile_url && $item->profile_url !== 'default.jpg' ? asset('storage/' . rawurlencode($item->profile_url)) : null;
            return $item;
        });

        return response()->json([
            'message' => 'Succes',
            'user_data' => $user_data
        ]);
    }

    public function update(Request $request){
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        DB::table('users')->where('id', $user->id)->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'instansi' => $validated['instansi'],
            'updated_at' => now()
        ]);

        $updated_user = DB::table('users')->where('id', $user->id)->first();

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui',
            'user_data_updated' => $updated_user
        ], 201);

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

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'profile_url' => $profile_url ? asset('storage/' . $profile_url) : null,
        ]);
    }
}
