<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Acquisition;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
			$table->uuid('uuid')->unique();
			$table->foreignIdFor(Acquisition::class)->nullable()->constrained()->cascadeOnDelete();
			$table->string('reference_no', 64)->nullable();
			$table->string('title', 124);
			$table->string('status', 64)->nullable();
			$table->morphs('assignable');
			$table->json('user_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
