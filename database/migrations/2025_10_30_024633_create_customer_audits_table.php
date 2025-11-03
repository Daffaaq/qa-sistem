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
        Schema::create('customer_audits', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->text('deskripsi_event');
            $table->date('tanggal_mulai_event');
            $table->date('tanggal_selesai_event')->nullable();
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
        Schema::dropIfExists('customer_audits');
    }
};
