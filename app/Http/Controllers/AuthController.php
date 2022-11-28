<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $field = $request->validate([
            'name' => 'required|string|max::100',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $field['name'],
            'email' => $field['email'],
            'password' => bcrypt($field['password']),
        ]);

        $token = $user->createToken('tokenku')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $field = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // check email
        $user = User::where('email', $field['email'])->first();

        // check password
        if (!$user || !Hash::check($field['password'], $user->password)) {
            return response(
                [
                    'message' => 'unauthorized',
                ],
                401
            );
        }

        $token = $user->createToken('tokenku')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return ['message' => 'Logged out'];
    }

    public function makeadmin(Request $request)
    {
        if ($request->user()->role == 0) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(
                    [
                        'status' => false,
                        'messages' =>
                            'emaiil ' . $request->email . ' tidak ditemukan',
                    ],
                    404
                );
            }

            $user->role = 0;

            if ($user->save()) {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => $request->email . ' telah menjadi admin',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => $request->email . ' gagal menjadi admin',
                    ],
                    500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                401
            );
        }
    }

    public function user(Request $request)
    {
        if ($request->user()->role == 0) {
            $user = User::all();
            return response()->json(
                [
                    'status' => true,
                    'data' => $user,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                401
            );
        }
    }

    public function getcontact()
    {
        $contact = ProfilPerusahaan::all()->first();
        return response()->json(
            [
                'status' => true,
                'Nama Perusahaan' => $contact->namaPerusahaan,
                'Alamat' => $contact->Alamat,
                'Alamat Email' => $contact->AlamatEmail,
                'No Telepon' => $contact->NoTelepon
            ],
            200
        );
    }

    public function updatecontact(Request $request)
    {
        if ($request->user()->role == 0) {
            $contact = ProfilPerusahaan::find(1);

            $contact->namaPerusahaan =
                $request->namaPerusahaan != null
                    ? $request->namaPerusahaan
                    : $contact->namaPerusahaan;
            $contact->Alamat =
                $request->Alamat != null ? $request->Alamat : $contact->Alamat;
            $contact->AlamatEmail =
                $request->AlamatEmail != null
                    ? $request->AlamatEmail
                    : $contact->AlamatEmail;
            $contact->NoTelepon =
                $request->NoTelepon != null
                    ? $request->NoTelepon
                    : $contact->NoTelepon;

            if ($contact->save()) {
                return response()->json(
                    [
                        'status' => true,
                        'message' => 'Profil Perusahaan telah berubah',
                        'data' => $contact,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Profil Perusahaan gagal berubah',
                    ],
                    500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                401
            );
        }
    }
}
