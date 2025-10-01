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
        Schema::create('maintenance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_documents_org');

            $table->foreignId('maintenance_operation_id')
                  ->constrained('maintenance_operations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_documents_operation');

            $table->string('name', 255);
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->string('file_type', 50); // 'image', 'pdf', 'document'
            $table->string('mime_type', 100);
            $table->bigInteger('file_size'); // en bytes
            $table->enum('document_type', ['invoice', 'report', 'photo_before', 'photo_after', 'warranty', 'other'])
                  ->index('idx_maintenance_documents_type');

            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // dimensions images, etc.

            $table->foreignId('uploaded_by')
                  ->constrained('users')
                  ->index('idx_maintenance_documents_uploader');

            $table->timestamps();

            // Index composé pour récupération par opération
            $table->index(['maintenance_operation_id', 'document_type'], 'idx_maintenance_documents_op_type');
            $table->index(['organization_id', 'file_type'], 'idx_maintenance_documents_org_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_documents');
    }
};