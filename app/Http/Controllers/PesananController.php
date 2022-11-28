<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DaftarKendaraan;
use App\Models\DaftarPesanan;
use App\Models\User;
use Carbon\Carbon;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role == 1) {
            $allpesanan = DaftarPesanan::where('idPengguna', $user->id)->where('Status', '!=', 'batal')->get();
        } else {
            $allpesanan = DaftarPesanan::all();

        }

        return response()->json(
            [
                'status' => true,
                'user' => $user,
                'pesanan' => $allpesanan,
            ],
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $field = $request->validate([
            'idKendaraan' => 'required|integer',
            'MulaiSewa' => 'required|date_format:Y-m-d',
            'BatasSewa' => 'required|date_format:Y-m-d',
        ]);

        $newpesanan = new DaftarPesanan();
        $kendaraan = DaftarKendaraan::find($request->idKendaraan);
        if (!$kendaraan) {
            return response()->json(
                [
                    'status' => true,
                    'messages' => 'id kendaraan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        $tgl1 = new Carbon($request->MulaiSewa);
        $tgl2 = new Carbon($request->BatasSewa);

        $LamaPinjaman = $tgl2->diff($tgl1);

        if ($tgl2 <= $tgl1) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'Batas Sewa tidak bisa sama atau sebelum ' . $tgl1 ,
                ],
                400
            );
        }
        $TotalTagihan = ($LamaPinjaman->d += 1) * $kendaraan->HargaSewaPerHari;

        $newpesanan->idPengguna = $request->user()->id;
        $newpesanan->idKendaraan = $request->idKendaraan;
        $newpesanan->MulaiSewa = $request->MulaiSewa;
        $newpesanan->BatasSewa = $request->BatasSewa;
        $newpesanan->TotalTagihan = $TotalTagihan;

        if ($kendaraan->Tersedia == 0) {
            return response()->json(
                [
                    'status' => true,
                    'messages' => 'Kendaraan Tidak Tersedia untuk Saat Ini',
                ],
                200
            );
        } elseif ($newpesanan->save()) {
            $kendaraan->Tersedia = 0;
            $kendaraan->save();

            return response()->json(
                [
                    'status' => true,
                    'messages' => 'Pesanan berhasil ditambahkan',
                    'data' => $newpesanan,
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'pesanan gagal ditambahkan',
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $pesanan = DaftarPesanan::find($id);
        if (!$pesanan) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id pesanan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        if (
            $request->user()->id == $pesanan->idPengguna ||
            $request->user()->role == 0
        ) {
            return response()->json(
                [
                    'staus' => true,
                    'data' => $pesanan,
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pesananku = DaftarPesanan::find($id);
        if (!$pesananku) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id pesanan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        $kendaraan = DaftarKendaraan::find($request->idKendaraan);
        if (!$kendaraan) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id kendaraan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }
        $idkendaraanLama = $pesananku->idKendaraan;

        $tgl1 = new Carbon(
            $request->MulaiSewa ? $request->MulaiSewa : $pesananku->MulaiSewa
        );
        $tgl2 = new Carbon(
            $request->BatasSewa ? $request->BatasSewa : $pesananku->BatasSewa
        );

        if ($tgl2 <= $tgl1) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'Batas Sewa tidak bisa sama atau sebelum ' . $tgl1 ,
                ],
                400
            );
        }

        $LamaPinjaman = $tgl2->diff($tgl1);
        $TotalTagihan = ($LamaPinjaman->d + 1) * $kendaraan->HargaSewaPerHari;

        $pesananku->idPengguna = $request->user()->id;
        $pesananku->idKendaraan = $request->idKendaraan
            ? $request->idKendaraan
            : $pesananku->idKendaraan;
        $pesananku->MulaiSewa = $request->MulaiSewa
            ? $request->MulaiSewa
            : $pesananku->MulaiSewa;
        $pesananku->BatasSewa = $request->BatasSewa
            ? $request->BatasSewa
            : $pesananku->BatasSewa;
        $pesananku->TotalTagihan = $TotalTagihan;

        if (
            $kendaraan->Tersedia == 0 &&
            $pesananku->idKendaraan != $idkendaraanLama
        ) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'Kendaraan Tidak Tersedia untuk Saat Ini',
                ],
                401
            );
        } elseif ($pesananku) {
            $pesananku->save();
            $kendaraanlama = DaftarKendaraan::find($idkendaraanLama);
            if (!$kendaraanlama) {
                return response()->json(
                    [
                        'status' => false,
                        'messages' =>
                            'id kendaraan ' . $id . ' tidak ditemukan',
                    ],
                    404
                );
            }
            $kendaraanlama->Tersedia = 1;
            $kendaraanlama->save();

            $kendaraan->Tersedia = 0;
            $kendaraan->save();

            return response()->json(
                [
                    'status' => true,
                    'messages' => 'Pesanan berhasil ditambahkan',
                    'data' => $pesananku,
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'pesanan gagal ditambahkan',
                ],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $pesananku = DaftarPesanan::find($id);
        if (!$pesananku) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id pesanan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        $kendaraan = DaftarKendaraan::find($pesananku->idKendaraan);
        if (!$kendaraan) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id pesanan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        if ($pesananku->idPengguna == $request->user()->id || $request->user()->role == 0) {
            $pesananku->status = 'batal';
            if ($pesananku->save()) {
                $kendaraan->Tersedia = 1;
                $kendaraan->save();
                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'data berhasil dihapus',
                        'data' => $pesananku,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => 'data gagal dihapus',
                    ],
                    500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => 401,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                401
            );
        }
    }
}
