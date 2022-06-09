<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            /* ----------------------------------------
            Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');
            // Уникальный слаг для изображения
            $table->string('slug', 128)
                ->unique()

                ->nullable()
                ->default(null);
            // Уникальная ссылка на оригинальный файл
            $table->string('source_url', 1024)
                ->nullable()
                ->default(null);
            // Предыдущий ID
            $table->bigInteger('source_id')
                ->unique()

                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Внешние ключи
            ---------------------------------------- */
            // Родительская новость (ID)
            $table->foreignId('news_id')
                ->nullable()
                ->default(null)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Родительская страница (ID)
            $table->foreignId('page_id')
                ->nullable()
                ->default(null)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Родительская страница (ID)
            $table->foreignId('gallery_video_id')
                ->nullable()
                ->default(null)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Родительская страница (ID)
            $table->foreignId('gallery_photo_id')
                ->nullable()
                ->default(null)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Обложка для страницы (ID)
            $table->bigInteger('thumbnail_id')
                ->nullable()
                ->unsigned()
                ->default(null)
                ->constrained();

            /* ----------------------------------------
            Булевы флаги
            ---------------------------------------- */
            // Пройдена ли обработка
            $table->boolean('is_processed')
                ->default(0);
            // Файл используется на странице
            $table->boolean('is_used')
                ->default(0);

            /* ----------------------------------------
            Контент файла
            ---------------------------------------- */
            // MIME type оригинального файла (название файла - original)
            $table->string('content_mime', 1024)
                ->nullable()
                ->default(null);
            // Путь к оригиналу
            $table->string('content_directory', 1024)
                ->nullable()
                ->default(null);
            // Формат оригинального файла
            $table->string('content_extension', 64)
                ->nullable()
                ->default(null);
            // Название оригинального файла
            $table->string('content_filename', 1024)
                ->nullable()
                ->default(null);
            // Тип файла
            $table->string('content_type', 255)
                ->default('other');
            // Конвертированные форматы (название файла - converted)
            $table->boolean('content_is_jpg')
                ->default(0);
            $table->boolean('content_is_png')
                ->default(0);
            $table->boolean('content_is_webp')
                ->default(0);
            $table->boolean('content_is_avif')
                ->default(0);
            $table->integer('content_sort')
                ->default(0);

            /* ----------------------------------------
            Временные отметки
            ---------------------------------------- */
            // Когда создан
            $table->timestamp('created_at')
                ->useCurrent();
            // Когда обновлён
            $table->timestamp('updated_at')
                ->useCurrentOnUpdate();
            // Когда удалена
            $table->timestamp('deleted_at')
                ->nullable()
                ->default(null);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->foreign('thumbnail_id')
                ->references('id')->on('news')
                ->onUpdate('cascade')
                ->nullOnDelete();
        });

        DB::statement('ALTER TABLE `files` ADD FULLTEXT full(source_url)');

        Self::createFolder();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `files` DROP INDEX full');
        Schema::dropIfExists('files');
        Self::deleteFiles();
    }

    private static function deleteFiles()
    {
        // Находим папку и удаляем
        $directory = public_path('files');
        if (File::isDirectory($directory)) {
            File::cleanDirectory($directory);
        }
    }

    private static function createFolder()
    {
        $directory = public_path('files');
        if (!File::exists($directory)) {
            File::makeDirectory($directory);
        } else {
            File::cleanDirectory($directory);
        }
    }
}
