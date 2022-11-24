<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarKendaraansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('JenisMobil');
            $table->longText('Deskripsi');
            $table->integer('Kapasitas');
            $table->boolean('Tersedia');
            $table->integer('HargaSewaPerHari');
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
        Schema::dropIfExists('daftar_kendaraans');
    }
}
