<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_favorites', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');


            /* ----------------------------------------
                Внешние ключи
            ---------------------------------------- */
            // Новость (ID)
            $table->bigInteger('news_id')
                ->nullable()
                ->unsigned()
                ->default(NULL)
                ->constrained();
        });

        Schema::table('news_favorites', function (Blueprint $table) {
            $table->foreign('news_id')
                ->references('id')->on('news')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_favorites');
    }
}
