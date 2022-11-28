<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarPesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_pesanans', function (Blueprint $table) {
            $table->id();
            $table->integer('idPengguna');
            $table->integer('idKendaraan');
            $table->date('MulaiSewa');
            $table->date('BatasSewa');
            $table->integer('TotalTagihan');
            $table->enum('Status', ['order', 'batal', 'lunas'])->default('order');
            $table->json('BuktiPembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daftar_pesanans');
    }
}
