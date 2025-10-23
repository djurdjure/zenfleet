<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add enterprise-grade features to documents table:
     * - status: Track document lifecycle
     * - is_latest_version: Support versioning
     * - GIN index on extra_metadata for fast JSON queries
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Add status column for document lifecycle management
            $table->string('status')->default('validated')->after('description');
            
            // Add version tracking
            $table->boolean('is_latest_version')->default(true)->after('status');
            
            // Add index on organization_id for faster multi-tenant queries
            $table->index('organization_id');
            
            // Add index on document_category_id for faster filtering
            $table->index('document_category_id');
            
            // Add index on status for lifecycle queries
            $table->index('status');
        });

        // Add GIN index on extra_metadata for fast JSON queries (PostgreSQL specific)
        if (Schema::connection($this->getConnection())->getConnection()->getDriverName() === 'pgsql') {
            // First, convert json to jsonb if needed (jsonb supports GIN natively)
            DB::statement('ALTER TABLE documents ALTER COLUMN extra_metadata TYPE jsonb USING extra_metadata::jsonb');
            
            // Create GIN index with jsonb_path_ops for optimal performance
            DB::statement('CREATE INDEX IF NOT EXISTS documents_extra_metadata_gin ON documents USING GIN (extra_metadata jsonb_path_ops)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop GIN index if exists (PostgreSQL specific)
        if (Schema::connection($this->getConnection())->getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS documents_extra_metadata_gin');
        }

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['document_category_id']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'is_latest_version']);
        });
    }
};
