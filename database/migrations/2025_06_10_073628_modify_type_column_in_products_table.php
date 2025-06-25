<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Đổi cột type thành enum với các giá trị cho phép
            $table->enum('type', ['simple', 'digital', 'variant'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Đổi lại thành varchar nếu cần rollback
            $table->string('type')->change();
        });
    }
}; 