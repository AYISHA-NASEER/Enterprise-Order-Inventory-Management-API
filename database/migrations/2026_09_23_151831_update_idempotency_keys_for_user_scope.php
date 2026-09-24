<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('idempotency_keys', function (Blueprint $table) {
            // Remove the global unique constraint
            $table->dropUnique(['key']);

            // Store a fingerprint of the checkout request
            $table->string('request_fingerprint')->nullable()->after('key');

            // The same key can be used by different users,
            // but not twice by the same user.
            $table->unique(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('idempotency_keys', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'key']);

            $table->dropColumn('request_fingerprint');

            $table->unique('key');
        });
    }
};