<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    public function getAllUser(){
        $user_data = DB::table('users')->orderByRaw('email_verified_at IS NOT NULL')->get();

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
}
