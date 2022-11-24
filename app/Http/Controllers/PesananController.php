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
    public function index()
    {
        $allpesanan = DaftarPesanan::all();

        return response()->json(
            [
                'success' => 200,
                'data' => $allpesanan,
            ],
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexuser(Request $request)
    {
        $user = $request->user();

        $pesanan = DaftarPesanan::where('idPengguna', $user->id)->get();

        return response()->json(
            [
                'success' => 200,
                'user' => $user,
                'riwayat pesanan' => $pesanan,
            ],
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newpesanan = new DaftarPesanan();
        $kendaraan = DaftarKendaraan::find($request->idKendaraan);

        $tgl1 = new Carbon($request->MulaiSewa);
        $tgl2 = new Carbon($request->BatasSewa);

        $LamaPinjaman = $tgl2->diff($tgl1);
        $TotalTagihan = ($LamaPinjaman->d + 1) * $kendaraan->HargaSewaPerHari;

        $newpesanan->idPengguna = $request->user()->id;
        $newpesanan->idKendaraan = $request->idKendaraan;
        $newpesanan->MulaiSewa = $request->MulaiSewa;
        $newpesanan->BatasSewa = $request->BatasSewa;
        $newpesanan->TotalTagihan = $TotalTagihan;

        if ($kendaraan->Tersedia == 0) {
            return response()->json(
                [
                    'success' => 200,
                    'messages' => 'Kendaraan Tidak Tersedia untuk Saat Ini',
                ],
                200
            );
        } elseif ($newpesanan->save()) {
            $kendaraan->Tersedia = 0;
            $kendaraan->save();

            return response()->json(
                [
                    'success' => 201,
                    'messages' => 'Pesanan berhasil ditambahkan',
                    'data' => $newpesanan,
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'success' => 400,
                    'messages' => 'pesanan gagal ditambahkan',
                ],
                400
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

        if (
            $request->user()->id == $pesanan->idPengguna ||
            $request->user()->role == 0
        ) {
            return response()->json(
                [
                    'success' => 200,
                    'data' => $pesanan,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'success' => 200,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                200
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
        $kendaraan = DaftarKendaraan::find($request->idKendaraan);
        $idkendaraanLama = $pesananku->idKendaraan;

        $tgl1 = new Carbon(
            $request->MulaiSewa ? $request->MulaiSewa : $pesananku->MulaiSewa
        );
        $tgl2 = new Carbon(
            $request->BatasSewa ? $request->BatasSewa : $pesananku->BatasSewa
        );

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
                    'success' => 200,
                    'messages' => 'Kendaraan Tidak Tersedia untuk Saat Ini',
                ],
                200
            );
        } elseif ($pesananku->save()) {
            $kendaraanlama = DaftarKendaraan::find($idkendaraanLama);
            $kendaraanlama->Tersedia = 1;
            $kendaraanlama->save();

            $kendaraan->Tersedia = 0;
            $kendaraan->save();

            return response()->json(
                [
                    'success' => 201,
                    'messages' => 'Pesanan berhasil ditambahkan',
                    'data' => $pesananku,
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'success' => 400,
                    'messages' => 'pesanan gagal ditambahkan',
                ],
                400
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
        $kendaraan = DaftarKendaraan::find($pesananku->idKendaraan);

        if( $pesananku->idPengguna == $request->user()->id){
            if ($pesananku->delete()) {
                $kendaraan->Tersedia = 1;
                $kendaraan->save();
                return response()->json(
                    [
                        'success' => 200,
                        'messages' => 'data berhasil dihapus',
                        'data' => $pesananku,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'success' => 400,
                        'messages' => 'data gagal dihapus',
                    ],
                    400
                );
            }
        }
        else{
            return response()->json(
                [
                    'success' => 200,
                    'message' =>
                        'Anda Tidak Memiliki Akses Terhadap Pesanan Ini',
                ],
                200
            );
        }

    }
}
