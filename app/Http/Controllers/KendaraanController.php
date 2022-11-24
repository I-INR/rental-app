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
        $kendaraans = DaftarKendaraan::all();
        $ringkasankendaraan = [];

        foreach ($kendaraans as $kendaraan) {
            $tampilkendaraan = new DaftarKendaraan();
            $tampilkendaraan->Jenis_Mobil = $kendaraan->JenisMobil;
            $tampilkendaraan->Deskripsi = $kendaraan->Deskripsi;

            array_push($ringkasankendaraan, $tampilkendaraan);
        }

        return response()->json(
            [
                'success' => 200,
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

        $newkendaraan->JenisMobil = $request->JenisMobil;
        $newkendaraan->Deskripsi = $request->Deskripsi;
        $newkendaraan->Kapasitas = $request->Kapasitas;
        $newkendaraan->Tersedia = $request->Tersedia ? $request->Tersedia : 1;
        $newkendaraan->HargaSewaPerHari = $request->HargaSewaPerHari;

        if ($newkendaraan->save()) {
            return response()->json(
                [
                    'success' => 201,
                    'messages' => 'data berhasil disimpan',
                    'data' => $newkendaraan,
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'success' => 400,
                    'messages' => 'data gagal disimpan',
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
    public function show($id)
    {
        $kendaraan = DaftarKendaraan::find($id);

        return response()->json(
            [
                'success' => 200,
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

        $kendaraan->JenisMobil = $request->JenisMobil
            ? $request->JenisMobil
            : $kendaraan->JenisMobil;
        $kendaraan->Deskripsi = $request->Deskripsi
            ? $request->Deskripsi
            : $kendaraan->Deskripsi;
        $kendaraan->Kapasitas = $request->Kapasitas
            ? $request->Kapasitas
            : $kendaraan->Kapasitas;
        $kendaraan->Tersedia = $request->Tersedia
            ? $request->Tersedia
            : $kendaraan->Tersedia;
        $kendaraan->HargaSewaPerHari = $request->HargaSewaPerHari
            ? $request->HargaSewaPerHari
            : $kendaraan->HargaSewaPerHari;

        if ($kendaraan->save()) {
            return response()->json(
                [
                    'success' => 200,
                    'messages' => 'data berhasil diperbarui',
                    'data' => $kendaraan,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'success' => 400,
                    'messages' => 'data gagal diperbarui',
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
    public function destroy($id)
    {
        $kendaraan = DaftarKendaraan::find($id);

        if ($kendaraan->delete()) {
            return response()->json(
                [
                    'success' => 200,
                    'messages' => 'data berhasil dihapus',
                    'data' => $kendaraan,
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
}
