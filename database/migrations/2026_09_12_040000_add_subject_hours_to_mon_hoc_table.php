<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mon_hoc', 'so_tiet_ly_thuyet')) {
            Schema::table('mon_hoc', function (Blueprint $table) {
                $table->unsignedSmallInteger('so_tiet_ly_thuyet')->default(0)->after('so_tin_chi');
            });
        }

        if (! Schema::hasColumn('mon_hoc', 'so_tiet_thuc_hanh')) {
            Schema::table('mon_hoc', function (Blueprint $table) {
                $table->unsignedSmallInteger('so_tiet_thuc_hanh')->default(0)->after('so_tiet_ly_thuyet');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mon_hoc', 'so_tiet_ly_thuyet')) {
            Schema::table('mon_hoc', function (Blueprint $table) {
                $table->dropColumn(['so_tiet_ly_thuyet', 'so_tiet_thuc_hanh']);
            });
        }
    }
};