<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vr_areas', function (Blueprint $table) {
            $table->json('media_index')->nullable()->after('slug');
            $table->json('skin_label')->nullable()->after('media_index');
        });
    }

    public function down(): void
    {
        Schema::table('vr_areas', function (Blueprint $table) {
            $table->dropColumn(['media_index','skin_label',]);
        });
    }
};
