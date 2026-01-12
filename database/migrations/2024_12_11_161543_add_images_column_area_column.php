<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagesColumnAreaColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vr_popups', function (Blueprint $table) {
            $table->longText('popup_images')->after('slug')->nullable() ;
            $table->string('area',50)->after('slug')->nullable() ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vr_popups', function (Blueprint $table) {
            $table->dropColumn('popup_images');
            $table->dropColumn('area');
        });
    }
}
