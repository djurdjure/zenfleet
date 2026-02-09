<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        if (!Schema::hasTable('vehicle_expenses') || !Schema::hasTable('expense_audit_logs')) {
            return;
        }

        DB::statement("
            CREATE OR REPLACE FUNCTION log_expense_changes()
            RETURNS TRIGGER AS $$
            DECLARE
                v_action TEXT;
                v_old_values JSONB;
                v_new_values JSONB;
                v_changed_fields JSONB;
                v_user_id BIGINT;
            BEGIN
                IF TG_OP = 'INSERT' THEN
                    v_action := 'created';
                    v_old_values := NULL;
                    v_new_values := to_jsonb(NEW);
                ELSIF TG_OP = 'UPDATE' THEN
                    v_action := 'updated';
                    v_old_values := to_jsonb(OLD);
                    v_new_values := to_jsonb(NEW);

                    IF OLD.approval_status != NEW.approval_status THEN
                        IF NEW.approval_status = 'approved' THEN
                            v_action := 'approved';
                        ELSIF NEW.approval_status = 'rejected' THEN
                            v_action := 'rejected';
                        END IF;
                    END IF;

                    IF OLD.payment_status != NEW.payment_status AND NEW.payment_status = 'paid' THEN
                        v_action := 'paid';
                    END IF;
                ELSIF TG_OP = 'DELETE' THEN
                    v_action := 'deleted';
                    v_old_values := to_jsonb(OLD);
                    v_new_values := NULL;
                END IF;

                -- Resolve actor ID from row payload, robust against column drift.
                v_user_id := COALESCE(
                    NULLIF(v_new_values->>'updated_by', '')::BIGINT,
                    NULLIF(v_new_values->>'recorded_by', '')::BIGINT,
                    NULLIF(v_old_values->>'updated_by', '')::BIGINT,
                    NULLIF(v_old_values->>'recorded_by', '')::BIGINT
                );

                IF TG_OP = 'UPDATE' THEN
                    SELECT jsonb_agg(key)
                    INTO v_changed_fields
                    FROM jsonb_each(v_old_values)
                    WHERE v_old_values->key IS DISTINCT FROM v_new_values->key;
                ELSE
                    v_changed_fields := '[]'::jsonb;
                END IF;

                INSERT INTO expense_audit_logs (
                    organization_id,
                    vehicle_expense_id,
                    user_id,
                    action,
                    action_category,
                    description,
                    old_values,
                    new_values,
                    changed_fields,
                    previous_status,
                    new_status,
                    previous_amount,
                    new_amount,
                    created_at
                ) VALUES (
                    COALESCE(NEW.organization_id, OLD.organization_id),
                    COALESCE(NEW.id, OLD.id),
                    v_user_id,
                    v_action,
                    CASE
                        WHEN v_action IN ('approved', 'rejected') THEN 'workflow'
                        WHEN v_action IN ('paid') THEN 'financial'
                        ELSE 'administrative'
                    END,
                    'Expense ' || v_action || ' for vehicle #' || COALESCE(NEW.vehicle_id, OLD.vehicle_id),
                    v_old_values,
                    v_new_values,
                    v_changed_fields,
                    CASE WHEN TG_OP = 'UPDATE' THEN OLD.approval_status ELSE NULL END,
                    CASE WHEN TG_OP != 'DELETE' THEN NEW.approval_status ELSE NULL END,
                    CASE WHEN TG_OP = 'UPDATE' THEN OLD.total_ttc ELSE NULL END,
                    CASE WHEN TG_OP != 'DELETE' THEN NEW.total_ttc ELSE NULL END,
                    NOW()
                );

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    public function down(): void
    {
        // Intentionally left as no-op to avoid restoring a broken trigger body.
    }
};
