<?php

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
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
			$table->string('slug', 12)->unique();
			$table->string('name', 64);
			$table->string('format', 128);
			$table->integer('value')->default(0);
			$table->boolean('daily_reset')->default(false);
			$table->boolean('monthly_reset')->default(false);
			$table->boolean('yearly_reset')->default(false);
			$table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
