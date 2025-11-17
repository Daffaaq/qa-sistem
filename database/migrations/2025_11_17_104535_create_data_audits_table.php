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
        Schema::create('data_audits', function (Blueprint $table) {
            $table->id();
            $table->string('temuan');
            $table->date('due_date');
            $table->string('status'); // open dan closed
            $table->string('pic');
            $table->string('file_evident')->nullable();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('customer_audits_id')->index();
            $table->foreign('customer_audits_id')->references('id')->on('customer_audits')->onDelete('cascade');
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
        Schema::dropIfExists('data_audits');
    }
};
