<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermalinksGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permalinks_groups', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');


            /* ----------------------------------------
                Контент группы ссылок
            ---------------------------------------- */
            // Название группы
            $table->string('content_title', 255);


            /* ----------------------------------------
                Сортировка и группировка
            ---------------------------------------- */
            // Сортировка по номеру
            $table->integer('sort_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permalinks_groups');
    }
}
