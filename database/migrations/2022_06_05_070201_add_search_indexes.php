<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `news` ADD FULLTEXT search(content_title)');
        DB::statement('ALTER TABLE `pages` ADD FULLTEXT search(content_title)');
        DB::statement('ALTER TABLE `gallery_videos` ADD FULLTEXT search(content_title)');
        DB::statement('ALTER TABLE `gallery_photos` ADD FULLTEXT search(content_title)');
        DB::statement('ALTER TABLE `files` ADD FULLTEXT search(content_filename)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `news` DROP INDEX search');
        DB::statement('ALTER TABLE `pages` DROP INDEX search');
        DB::statement('ALTER TABLE `gallery_videos` DROP INDEX search');
        DB::statement('ALTER TABLE `gallery_photos` DROP INDEX search');
        DB::statement('ALTER TABLE `files` DROP INDEX search');
    }
};
