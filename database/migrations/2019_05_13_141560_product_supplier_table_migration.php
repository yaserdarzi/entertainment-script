<?php

use App\Inside\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductSupplierTableMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::PRODUCT_SUPPLIER_DB, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('app_id');
            $table->bigInteger('supplier_id');
            $table->bigInteger('product_id');
            $table->json('info')->nullable();
            $table->timestamps();
        });
        Schema::table(Constants::PRODUCT_SUPPLIER_DB, function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on(Constants::PRODUCT_DB)->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::PRODUCT_SUPPLIER_DB);
    }
}
