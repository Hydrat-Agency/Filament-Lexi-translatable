<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('lexi-translate.table_name','lexi_translations'), function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->string('locale', 5);
            $table->text('text');
            $table->string('column');
            $table->timestamps();

            $table->index(['translatable_type', 'translatable_id', 'locale', 'column'], 'translatable_locale_column_index');
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'column'], 'unique_translation_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('lexi-translate.table_name','lexi_translations'));
    }

};
