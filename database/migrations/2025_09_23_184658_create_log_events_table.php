<?php

use App\Models\EventName;
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
        Schema::create('log_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Market::class)->unsigned()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EventName::class)->unsigned()->constrained()->cascadeOnDelete();
            $table->string('session_id');
            $table->string('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_events');
    }
};
