<?php

use App\Models\Market;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_service_titan_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Market::class)->unsigned()->constrained()->cascadeOnDelete();
            $table->datetime('start');
            $table->datetime('end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_service_titan_jobs');
    }
};
