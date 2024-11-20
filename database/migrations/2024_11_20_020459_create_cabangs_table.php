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
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id(); // Primary key auto increment
            $table->string('cabang_id', 10)->unique(); // Unique column for cabang_id
            $table->string('nama'); // Nama cabang
            $table->string('alamat'); // Alamat cabang
            $table->string('telp'); // Telepon cabang
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cabangs');
    }
};
