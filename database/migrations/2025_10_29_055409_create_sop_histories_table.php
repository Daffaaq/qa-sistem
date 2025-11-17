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
        Schema::create('sop_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sop_id')->index();
            $table->foreign('sop_id')->references('id')->on('sops')->onDelete('cascade');
            $table->string('title_document');
            $table->string('file_document');
            $table->date('date_document');
            $table->time('time_document');
            $table->integer('revision_number');
            $table->boolean('is_active');
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('sop_histories');
    }
};
