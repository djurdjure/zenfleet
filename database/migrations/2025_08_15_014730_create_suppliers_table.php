<?php

//database/migrations/2025_08_15_014730_create_suppliers_table.php

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
        Schema::create("suppliers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("organization_id")->constrained()->onDelete("cascade");
            $table->string("name");
            $table->string("contact_person")->nullable();
            $table->string("phone", 50)->nullable();
            $table->string("email")->nullable();
            $table->text("address")->nullable();
            $table->string("website")->nullable();
            $table->text("notes")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(["organization_id", "name", "deleted_at"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("suppliers");
    }
};
