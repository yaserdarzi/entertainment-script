<?php

use App\Inside\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductEpisodeTableMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::PRODUCT_EPISODE_DB, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('app_id');
            $table->bigInteger('product_id');
            $table->bigInteger('supplier_id');
            $table->bigInteger('capacity')->default(1);
            $table->bigInteger('capacity_power_up')->default(1);
            $table->bigInteger('capacity_filled')->default(0);
            $table->bigInteger('capacity_remaining')->default(1);
            $table->bigInteger('price_adult')->default(0);
            $table->bigInteger('price_adult_power_up')->default(0);
            $table->bigInteger('price_child')->default(0);
            $table->bigInteger('price_child_power_up')->default(0);
            $table->bigInteger('price_baby')->default(0);
            $table->bigInteger('price_baby_power_up')->default(0);
            $table->string('type_percent')->default(Constants::TYPE_PERCENT_PERCENT);
            $table->bigInteger('percent')->default(0);
            $table->string('title')->nullable();
            $table->timestamp('date');
            $table->string('start_hours')->nullable();
            $table->string('end_hours')->nullable();
            $table->string('status')->default(Constants::STATUS_ACTIVE);
            $table->timestamps();
        });
        Schema::table(Constants::PRODUCT_EPISODE_DB, function (Blueprint $table) {
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
        Schema::dropIfExists(Constants::PRODUCT_EPISODE_DB);
    }
}
