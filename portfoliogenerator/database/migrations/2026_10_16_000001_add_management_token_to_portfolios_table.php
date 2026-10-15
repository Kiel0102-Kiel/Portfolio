<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('management_token', 64)->nullable()->unique();
        });

        DB::table('portfolios')->whereNull('management_token')->orderBy('id')->chunkById(100, function ($portfolios) {
            foreach ($portfolios as $portfolio) {
                DB::table('portfolios')->where('id', $portfolio->id)->update([
                    'management_token' => Str::random(64),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropUnique('portfolios_management_token_unique');
            $table->dropColumn('management_token');
        });
    }
};
