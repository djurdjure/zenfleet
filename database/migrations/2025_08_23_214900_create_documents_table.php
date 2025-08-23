<?php

// database/migrations/2025_08_23_183001_create_documents_table.php

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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->comment('User who uploaded the document')->constrained()->onDelete('cascade');

            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_in_bytes');

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->text('description')->nullable();
            $table->json('extra_metadata')->nullable();

            $table->timestamps();
        });

        Schema::create('documentables', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->morphs('documentable'); // Creates `documentable_id` and `documentable_type`
            $table->primary(['document_id', 'documentable_id', 'documentable_type'], 'documentables_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentables');
        Schema::dropIfExists('documents');
    }
};
