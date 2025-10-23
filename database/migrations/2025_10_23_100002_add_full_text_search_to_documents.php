<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add PostgreSQL Full-Text Search capabilities to documents table.
     * Creates a tsvector column and GIN index for ultra-fast search.
     */
    public function up(): void
    {
        // Only run for PostgreSQL
        if (Schema::connection($this->getConnection())->getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Add search_vector column with generated content
        // Note: extra_metadata is now jsonb after migration 2025_10_23_100000
        DB::statement("
            ALTER TABLE documents 
            ADD COLUMN search_vector tsvector 
            GENERATED ALWAYS AS (
                setweight(to_tsvector('french', coalesce(original_filename, '')), 'A') ||
                setweight(to_tsvector('french', coalesce(description, '')), 'B') ||
                setweight(to_tsvector('french', coalesce(extra_metadata::text, '')), 'C')
            ) STORED
        ");

        // Create GIN index on search_vector for fast full-text search
        DB::statement('CREATE INDEX documents_search_vector_idx ON documents USING GIN (search_vector)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run for PostgreSQL
        if (Schema::connection($this->getConnection())->getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS documents_search_vector_idx');
        DB::statement('ALTER TABLE documents DROP COLUMN IF EXISTS search_vector');
    }
};
