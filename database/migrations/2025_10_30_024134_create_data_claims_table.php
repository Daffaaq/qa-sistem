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
        Schema::create('data_claims', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_claim');
            $table->string('customer');
            $table->string('part_no');
            $table->string('problem');
            $table->integer('quantity');
            $table->string('klasifikasi');
            $table->string('kategori');
            $table->string('file_evident')->nullable();
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
        Schema::dropIfExists('data_claims');
    }
};
