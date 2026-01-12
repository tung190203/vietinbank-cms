<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->unique();
            $table->string('slug', 50)->unique()->nullable();
            $table -> string('excerpt',255)->nullable() ;
            $table -> string('category',50)->nullable() ;
        
            $table -> longText('product_images')->nullable() ;
            $table -> longText('video_urls')->nullable() ;
            $table -> longText('description')->nullable() ;
        
            //   ------------------------------------------
            $table->string('order_no', 11)->default(999999);
            $table->string('lang_code', 10)->default("en");
            $table->unsignedInteger('state')->default(1);

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
        Schema::dropIfExists('products');
    }
}
