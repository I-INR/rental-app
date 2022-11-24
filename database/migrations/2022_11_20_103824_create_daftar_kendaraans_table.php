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
            $table->integer('KapasitasTempatDuduk');
            $table->integer('Tenaga');
            $table->integer('KapasitasMesin');
            $table->integer('KapasitasBahanBakar');
            $table->integer('HargaSewaPerHari');
            $table->boolean('Tersedia');
            $table->timestamps();
        });

        DB::table('daftar_kendaraans')->insert(
            [
                [
                    'id' => 1,
                    'JenisMobil' => 'Toyota Veloz',
                    'Deskripsi' => 'Mantap',
                    'KapasitasTempatDuduk' => 7,
                    'Tenaga' => 95,
                    'KapasitasMesin' => 1496,
                    'KapasitasBahanBakar' => 43,
                    'HargaSewaPerHari' => 300000,
                    'Tersedia' => 1,
                ],
                [
                    'id' => 2,
                    'JenisMobil' => 'Honda Brio',
                    'Deskripsi' => 'Mantap',
                    'KapasitasTempatDuduk' => 5,
                    'Tenaga' => 89,
                    'KapasitasMesin' => 1199,
                    'KapasitasBahanBakar' => 35,
                    'HargaSewaPerHari' => 250000,
                    'Tersedia' => 1,
                ],
            ]
        );
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
