<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_dummies', function (Blueprint $table) {
            $table->id();
            $table->integer('ngnonofficial'); 
            $table->integer('ngofficial'); 
            $table->string('customer'); // PT. YAMAHA INDONESIA MOTOR MFG, PT MITSUBISHI KRAMA YUDHA TIGA BERLIANMOTORS, PT ASAHI DENSO INDONESIA
            $table->string('kodecustomer');
            $table->string('tipartname');
            $table->string('tipartnumber');
            $table->string('tsidiid');
            $table->integer('bulan');
            $table->bigInteger('total_kirim');
            $table->year('tahun');
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
        Schema::dropIfExists('data_dummies');
    }
};
