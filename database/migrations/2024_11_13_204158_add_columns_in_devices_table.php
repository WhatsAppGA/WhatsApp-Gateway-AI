<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->boolean('wh_read')->default(0);
            $table->boolean('wh_typing')->default(0);
            $table->integer('delay')->default(0);
            $table->boolean('set_available')->default(0);
            $table->string('gptkey')->nullable();
            $table->string('geminikey')->nullable();
            $table->boolean('reject_call')->default(0);

            // New columns
            $table->enum('gemini_status', ['enabled', 'disabled'])->default('disabled');
            $table->string('gemini_model', 50)->nullable();
            $table->string('gemini_api_key', 200)->nullable();
            $table->text('gemini_instructions')->nullable();
            $table->enum('transcription_status', ['enabled', 'disabled'])->default('disabled');
            $table->string('transcription_model', 50)->nullable();
            $table->string('huggingface_api_key', 200)->nullable();
            $table->enum('auto_status_save', ['enabled', 'disabled'])->default('disabled');
            $table->enum('auto_status_forward', ['enabled', 'disabled'])->default('disabled');
            $table->enum('status_nudity_detection', ['enabled', 'disabled'])->default('disabled');
            $table->enum('chat_nudity_detection', ['enabled', 'disabled'])->default('disabled');
        });
        
        Schema::create('ai_chats', function (Blueprint $table) {
            $table->increments('id'); // Auto-increment primary key
            $table->string('sender_id', 20);
            $table->string('number', 20);
            $table->string('role', 200);
            $table->text('message')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->integer('is_favorite')->default(0); // Replace 'column_name' with the column after which this should be added
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            //
        });
    }
}
