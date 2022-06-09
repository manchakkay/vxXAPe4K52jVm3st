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
        Schema::create('globals', function (Blueprint $table) {
            // Первичный ключ
            $table->bigIncrements('id');
            // Ключ
            $table->string('key', 512)
                ->unique()

                ->nullable()
                ->default(null);
            // Значение
            $table->string('value', 2048);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('globals');
    }
};
