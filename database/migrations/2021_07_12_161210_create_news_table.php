<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            /* ----------------------------------------
            Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');
            // Уникальный слаг для адреса страницы
            $table->string('slug', 512)
                ->unique()

                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Внешние ключи
            ---------------------------------------- */
            // Категория новости (ID)
            $table->foreignId('news_category_id')
                ->nullable()
                ->default(null)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');

            /* ----------------------------------------
            Булевы флаги
            ---------------------------------------- */
            // Показывать ли новость
            $table->boolean('is_visible')
                ->default(0);
            // Пройден ли процессинг
            $table->boolean('is_processed')
                ->default(0);
            $table->boolean('is_event')
                ->default(0);
            $table->string('render_hash', 512)
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Контент новости
            ---------------------------------------- */
            // Заголовок новости
            $table->string('content_title', 512);
            /* $table->string('content_thumbnail', 1024)
            ->nullable()
            ->default(NULL); */
            // Описание новости
            $table->text('content_description')
                ->nullable()
                ->default(null);
            // Контент новостей в JSON формате
            $table->json('content_json')
                ->nullable()
                ->default(null);
            // HTML код с содержимым
            $table->longText('content_html')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Мета-информация для OpenGraph
            ---------------------------------------- */
            // Заголовок новости
            $table->string('meta_title', 512)
                ->nullable()
                ->default(null);
            // Описание новости
            $table->text('meta_description')
                ->nullable()
                ->default(null);
            // Дополнительные мета-данные
            $table->json('meta_json')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Временные отметки
            ---------------------------------------- */
            // Когда создана
            $table->timestamp('created_at')
                ->useCurrent();
            // Когда обновлена
            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate();
            // Когда удалена (если удалена)
            $table->timestamp('deleted_at')
                ->nullable()
                ->default(null);
            // Когда опубликована (если опубликована)
            $table->timestamp('published_at')
                ->nullable()
                ->default(null);
            // Когда обработана
            $table->timestamp('processed_at')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Переменные для мероприятия
            ---------------------------------------- */
            // Дата мероприятия (если это мероприятие)
            $table->datetime('event_date')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Импортированные новости
            ---------------------------------------- */
            // Флаг импортированных новостей
            $table->boolean('is_imported')
                ->default(0);
            // Импортированный контент
            $table->longText("content_imported")
                ->nullable()
                ->default(null);
            // Предыдущий slug
            $table->text('old_slug')
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
