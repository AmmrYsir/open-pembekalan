<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\Agency;
use App\Models\Subagency;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agency_officers', function (Blueprint $table) {
            $table->id();
			$table->uuid('uuid')->unique();
			$table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
			$table->foreignIdFor(Agency::class, 'agency_id')->constrained()->onDelete('cascade');
			$table->foreignIdFor(Subagency::class, 'subagency_id')->nullable()->constrained()->onDelete('cascade');
			$table->string('title', 64)->nullable();
			$table->string('nric', 12)->nullable();
            $table->string('position', 128)->nullable();
			$table->string('mobile_number', 16)->nullable();
			$table->string('home_phone_number', 16)->nullable();
			$table->foreignIdFor(User::class, 'created_by')->nullable()->constrained('users')->onDelete('set null');
			$table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_officers');
    }
};
