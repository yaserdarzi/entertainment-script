<?php

use App\Inside\Constants;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ProductMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::PRODUCT_DB, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('app_id');
            $table->string('title');
            $table->string('image');
            $table->longText('small_desc')->nullable();
            $table->longText('desc')->nullable();
            $table->longText('rule')->nullable();
            $table->longText('recovery')->nullable();
            $table->integer('sort')->default(1);
            $table->integer('star')->default(1);
            $table->json('info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::PRODUCT_DB);
    }
}
