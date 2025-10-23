<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Create document_revisions table for version history tracking.
     * Enterprise-grade feature for audit and compliance.
     */
    public function up(): void
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->comment('User who created this revision')->constrained()->onDelete('cascade');
            
            // Store the previous version's file information
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_in_bytes');
            
            // Store the previous metadata (using jsonb for PostgreSQL compatibility)
            $table->jsonb('extra_metadata')->nullable();
            $table->text('description')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Revision tracking
            $table->unsignedInteger('revision_number');
            $table->text('revision_notes')->nullable()->comment('Why this revision was created');
            
            $table->timestamps();
            
            // Indexes
            $table->index('document_id');
            $table->index(['document_id', 'revision_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
    }
};
