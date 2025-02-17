<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('venues', function (Blueprint $table) {
            if (!Schema::hasColumn('venues', 'status')) {
                $table->enum('status', ['available', 'occupied', 'maintenance', 'unavailable'])
                    ->default('available');
            }
        });
    }

    public function down()
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 