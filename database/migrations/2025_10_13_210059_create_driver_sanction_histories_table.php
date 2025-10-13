<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table driver_sanction_histories
 *
 * Cette table assure la traçabilité complète de toutes les actions
 * effectuées sur les sanctions (création, modification, archivage).
 * Conforme aux exigences d'audit enterprise-grade.
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_sanction_histories', function (Blueprint $table) {
            // Clés primaires et timestamps
            $table->id();
            $table->timestamps();

            // Relation avec la sanction
            $table->foreignId('sanction_id')
                ->constrained('driver_sanctions')
                ->onDelete('cascade')
                ->comment('Sanction concernée par cet événement');

            // Utilisateur ayant effectué l'action
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Utilisateur ayant effectué l\'action');

            // Type d'action effectuée
            $table->enum('action', [
                'created',
                'updated',
                'archived',
                'unarchived',
                'deleted'
            ])->comment('Type d\'action effectuée sur la sanction');

            // Détails de l'action en JSON
            $table->json('details')
                ->comment('Détails de l\'action (changements effectués, valeurs avant/après)');

            // Adresse IP de l'utilisateur (pour audit de sécurité)
            $table->string('ip_address', 45)->nullable()
                ->comment('Adresse IP de l\'utilisateur lors de l\'action');

            // User agent (navigateur)
            $table->string('user_agent', 500)->nullable()
                ->comment('User agent du navigateur');

            // Index pour optimiser les requêtes d'audit
            $table->index('sanction_id', 'idx_history_sanction');
            $table->index('user_id', 'idx_history_user');
            $table->index('action', 'idx_history_action');
            $table->index('created_at', 'idx_history_created');

            // Index composé pour requêtes d'audit détaillées
            $table->index(['sanction_id', 'created_at'], 'idx_history_sanction_date');
            $table->index(['user_id', 'action'], 'idx_history_user_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_sanction_histories');
    }
};
