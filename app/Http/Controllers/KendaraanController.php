<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DaftarKendaraan;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kendaraans = DaftarKendaraan::where('Status', 1)->get();
        $ringkasankendaraan = [];

        foreach ($kendaraans as $kendaraan) {
            $tampilkendaraan = new DaftarKendaraan();
            $tampilkendaraan->id = $kendaraan->id;
            $tampilkendaraan->Jenis_Mobil = $kendaraan->JenisMobil;
            $tampilkendaraan->Deskripsi = $kendaraan->Deskripsi;
            $tampilkendaraan->HargaSewaPerHari = $kendaraan->HargaSewaPerHari;

            array_push($ringkasankendaraan, $tampilkendaraan);
        }

        return response()->json(
            [
                'status' => true,
                'data' => $ringkasankendaraan,
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
        $newkendaraan = new DaftarKendaraan();

        $field = $request->validate([
            'JenisMobil' => 'required|string',
            'Deskripsi' => 'required|string',
            'KapasitasTempatDuduk' => 'required|integer',
            'Tenaga' => 'required|integer',
            'KapasitasMesin' => 'required|integer',
            'KapasitasBahanBakar' => 'required|integer',
            'HargaSewaPerHari' => 'required|integer',
            'Tersedia' => 'in:0,1',
        ]);

        $newkendaraan->JenisMobil = $request->JenisMobil;
        $newkendaraan->Deskripsi = $request->Deskripsi;
        $newkendaraan->KapasitasTempatDuduk = $request->KapasitasTempatDuduk;
        $newkendaraan->Tenaga = $request->Tenaga;
        $newkendaraan->KapasitasMesin = $request->KapasitasMesin;
        $newkendaraan->KapasitasBahanBakar = $request->KapasitasBahanBakar;
        $newkendaraan->HargaSewaPerHari = $request->HargaSewaPerHari;
        $newkendaraan->Tersedia =
            $request->Tersedia != null ? $request->Tersedia : 1;

        if ($request->user()->role == 0) {
            if ($newkendaraan->save()) {
                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'data berhasil disimpan',
                        'data' => $newkendaraan,
                    ],
                    201
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => 'data gagal disimpan',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kendaraan = DaftarKendaraan::find($id);
        if (!$kendaraan || $kendaraan->Status == 0) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id Kendaraan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }

        return response()->json(
            [
                'status' => true,
                'data' => $kendaraan,
            ],
            200
        );
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
        $kendaraan = DaftarKendaraan::find($id);
        if (!$kendaraan || $kendaraan->Status == 0) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id Kendaraan ' . $id . ' tidak ditemukan',
                ],
                404
            );
        }
        $field = $request->validate([
            'JenisMobil' => 'string',
            'Deskripsi' => 'string',
            'KapasitasTempatDuduk' => 'integer',
            'Tenaga' => 'integer',
            'KapasitasMesin' => 'integer',
            'KapasitasBahanBakar' => 'integer',
            'HargaSewaPerHari' => 'integer',
            'Tersedia' => 'in:0,1',
        ]);

        $kendaraan->JenisMobil =
            $request->JenisMobil != null
                ? $request->JenisMobil
                : $kendaraan->JenisMobil;
        $kendaraan->Deskripsi =
            $request->Deskripsi != null
                ? $request->Deskripsi
                : $kendaraan->Deskripsi;
        $kendaraan->KapasitasTempatDuduk =
            $request->KapasitasTempatDuduk != null
                ? $request->KapasitasTempatDuduk
                : $kendaraan->KapasitasTempatDuduk;
        $kendaraan->Tenaga =
            $request->Tenaga != null ? $request->Tenaga : $kendaraan->Tenaga;
        $kendaraan->KapasitasMesin =
            $request->KapasitasMesin != null
                ? $request->KapasitasMesin
                : $kendaraan->KapasitasMesin;
        $kendaraan->KapasitasBahanBakar =
            $request->KapasitasBahanBakar != null
                ? $request->KapasitasBahanBakar
                : $kendaraan->KapasitasBahanBakar;
        $kendaraan->HargaSewaPerHari =
            $request->HargaSewaPerHari != null
                ? $request->HargaSewaPerHari
                : $kendaraan->HargaSewaPerHari;
        $kendaraan->Tersedia =
            $request->Tersedia != null
                ? $request->Tersedia
                : $kendaraan->Tersedia;

        if ($request->user()->role == 0) {
            if ($kendaraan->save()) {
                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'data berhasil diperbarui',
                        'data' => $kendaraan,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => 'data gagal diperbarui',
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $kendaraan = DaftarKendaraan::find($id);
        if (!$kendaraan || $kendaraan->Status == 0) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'id kendaraan ' . $id . 'tidak ditemukan',
                ],
                404
            );
        }

        if ($request->user()->role == 0) {
            $kendaraan->status = 0;
            if ($kendaraan->save()) {
                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'data berhasil dihapus',
                        'data' => $kendaraan,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'data gagal dihapus',
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
