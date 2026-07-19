<?php

use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
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
        Schema::create('acquisitions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 14)->nullable();
            $table->string('method', 24)->nullable();
            $table->string('project_number')->nullable();
            $table->string('project_name')->nullable();
            $table->string('status')->nullable();
            $table->string('provision_type', 24)->nullable();
            $table->string('submission_type')->nullable();
            $table->foreignIdFor(VotType::class, 'vot_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('tender_number', 56)->nullable();
            $table->decimal('siling_price', 12, 2)->nullable();
            $table->string('no_allocation_warrant', 56)->nullable();
            $table->foreignIdFor(Agency::class, 'agency_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Subagency::class, 'subagency_id')->nullable()->constrained()->onDelete('cascade');

            // requirements
            $table->boolean('is_required_kbp')->comment('KBP - Kontrak Panel Berpusat')->nullable();
            $table->boolean('mof_required')->nullable();
            $table->boolean('cidb_required')->nullable();
            $table->string('committee_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acquisitions');
    }
};
