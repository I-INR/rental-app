<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DaftarPesanan;
use App\Models\Pembayaran;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return response(Carbon::now());
        $field = $request->validate([
            'IdPesanan' => 'required|integer',
            'TotalBayar' => 'required|integer',
        ]);

        $pesanan = DaftarPesanan::find($request->IdPesanan);
        if (!$pesanan) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'Tidak ada pesanan dengan id ' . $request->IdPesanan,
                ],
                404
            );
        }
        else if( $pesanan->Status == 'lunas'){
            return response()->json(
                [
                    'status' => true,
                    'messages' => 'pesanan id ' . $pesanan->id . 'telah dibayar',
                ],
                200
            );
        }

        $kembalian = $request->TotalBayar - $pesanan->TotalTagihan;
        if ($kembalian < 0) {
            return response()->json(
                [
                    'status' => false,
                    'messages' => 'Uang Anda kurang dari tagihan',
                    'TotalTagihan' => $pesanan->TotalTagihan,
                ],
                400
            );
        }

        $bayar = new Pembayaran();

        $bayar->IdPesanan = $field['IdPesanan'];
        $bayar->TotalBayar = $field['TotalBayar'];
        $bayar->Kembalian = $kembalian;
        $bayar->TanggalPembayaran = Carbon::now();

        if ($request->user()->role == 0) {
            if ($bayar->save()) {
                $pesanan->Status = 'lunas';
                $pesanan->BuktiPembayaran = $bayar;
                $pesanan->save();

                return response()->json(
                    [
                        'status' => true,
                        'messages' => 'Pembayaran berhasil dilakukan',
                        'data' => $pesanan,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'messages' => 'Pembayaran gagal dilakukan',
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
