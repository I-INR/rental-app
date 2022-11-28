<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilPerusahaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profil_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('namaPerusahaan');
            $table->string('Alamat');
            $table->string('AlamatEmail');
            $table->string('NoTelepon');
            $table->timestamps();
        });

        DB::table('profil_perusahaans')->insert([
            'id' => 1,
            'namaPerusahaan' => 'IN-Rental',
            'Alamat' => 'Purwokerto',
            'AlamatEmail' => 'IN-Rental@gmail.com',
            'NoTelepon' => '085292365482',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil_perusahaans');
    }
}
