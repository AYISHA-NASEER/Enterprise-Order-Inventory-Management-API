<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');

            $table->string('status')
                ->default('active')
                ->after('price');

            $table->string('source')
                ->nullable()
                ->after('status');

            $table->unsignedBigInteger('external_id')
                ->nullable()
                ->after('source');

            $table->json('external_data')
                ->nullable()
                ->after('external_id');

            $table->unique(
                ['source', 'external_id'],
                'products_source_external_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_source_external_id_unique');

            $table->dropColumn([
                'description',
                'status',
                'source',
                'external_id',
                'external_data',
            ]);
        });
    }
};