<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE autoreplies MODIFY COLUMN type ENUM('text', 'button', 'image', 'template', 'list', 'media', 'vcard', 'location', 'sticker')");
        // add is_read is_typing delay to autoreplies
        Schema::table('autoreplies', function (Blueprint $table) {
            $table->boolean('is_read')->default(0);
            $table->boolean('is_typing')->default(0);
            $table->integer('delay')->default(0);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE autoreplies MODIFY COLUMN type ENUM('text', 'button', 'image', 'template', 'list', 'media')");
    }
};
