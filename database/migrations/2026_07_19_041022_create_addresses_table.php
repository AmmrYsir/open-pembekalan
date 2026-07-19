<?php

use App\Models\State;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->string('address_line_1', 124);
            $table->string('address_line_2', 124)->nullable();
            $table->string('address_line_3', 124)->nullable();
            $table->string('postal_code', 6);
            $table->string('district', 124)->nullable();
            $table->string('city', 124);
            $table->foreignIdFor(State::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
