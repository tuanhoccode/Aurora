<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id')->comment('Brand ID');
            $table->string('name', 100)->unique()->comment('Brand name (unique)');
            $table->string('logo', 255)->nullable()->comment('Brand logo');
            $table->boolean('is_active')->default(1)->comment('1 if brand is active, 0 if inactive');
            $table->timestamps(0);
            $table->softDeletes()->comment('Soft delete timestamp');
        });
    }

    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
