<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVrPopupGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vr_popup_groups', function (Blueprint $table) {
            $table->id();

            $table -> string('name',100) -> unique();
            $table -> string('slug',50)-> unique()->nullable() ;

            //   ------------------------------------------
            $table->string('order_no',11) ->default( 999999 ) ;
            $table->string('lang_code',10) ->default( "en" ) ;
            $table->unsignedInteger('state')->default( 1 ) ;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vr_popup_groups');
    }
}
