<?php

use App\Models\User;
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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('ssm_type')->nullable();
            $table->string('ssm_number')->nullable();
            $table->string('old_registration_number')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('telephone_no')->nullable();
            $table->string('operating_area')->nullable();
            $table->date('established_date')->nullable();
            $table->string('website_link')->nullable();
            $table->string('cert_verified_code')->nullable();
            $table->string('tax_reference_number')->nullable();
            $table->string('cjcp_reference_number')->nullable();
            $table->date('ssm_start_date')->nullable();
            $table->date('ssm_expiry_date')->nullable();
            $table->date('kpb_active_date')->nullable();
            $table->date('kpb_expiry_date')->nullable();
            $table->date('cidb_active_date')->nullable();
            $table->date('cidb_bumiputera_active_date')->nullable();
            $table->date('cidb_expiry_date')->nullable();
            $table->date('cidb_bumiputera_expiry_date')->nullable();
            $table->date('mof_active_date')->nullable();
            $table->date('mof_bumiputera_active_date')->nullable();
            $table->date('mof_expiry_date')->nullable();
            $table->date('mof_bumiputera_expiry_date')->nullable();
            $table->string('cidb_cert_no')->nullable();
            $table->string('cidb_bumiputera_cert_no')->nullable();
            $table->string('cidb_gred_id')->nullable();
            $table->boolean('cidb_bumi_status')->nullable();
            $table->string('mof_registration_no')->nullable();
            $table->string('mof_bumiputera_registration_no')->nullable();
            $table->boolean('mof_bumi_status')->nullable();
            $table->boolean('kpb_status')->nullable();
            $table->decimal('paid_up_capital', 15, 2)->nullable();
            $table->decimal('authorized_capital', 15, 2)->nullable();
            // Application process tracking fields
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->string('application_status')->nullable();
            $table->foreignIdFor(User::class, 'application_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('application_reviewed_at')->nullable();
            $table->foreignIdFor(User::class, 'application_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('application_approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
