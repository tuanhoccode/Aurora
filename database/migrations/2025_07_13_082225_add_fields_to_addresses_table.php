<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAddressesTable extends Migration
{
    public function up()
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('province')->nullable()->after('address');
            $table->string('district')->nullable()->after('province');
            $table->string('ward')->nullable()->after('district');
            $table->string('street')->nullable()->after('ward');
            $table->decimal('latitude', 10, 8)->nullable()->after('street');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->enum('address_type', ['home', 'office'])->nullable()->after('longitude');
            $table->string('email')->nullable()->after('address_type');
        });
    }

    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward', 'street', 'latitude', 'longitude', 'address_type', 'email']);
        });
    }
}