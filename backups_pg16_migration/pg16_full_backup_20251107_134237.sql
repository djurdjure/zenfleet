--
-- PostgreSQL database cluster dump
--

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Roles
--

CREATE ROLE zenfleet_user;
ALTER ROLE zenfleet_user WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS PASSWORD 'SCRAM-SHA-256$4096:sLUVO31LyEx1CO20gANVig==$Bvp+S78A8WIqhdOuyMmCDq53EeiAMyyH69+LYv0cfSA=:/2uLEziA+qnsh95y2T8u8/RHszmqdUNUvmeDJkKPmt8=';

--
-- User Configurations
--








--
-- Databases
--

--
-- Database "template1" dump
--

\connect template1

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

\connect postgres

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- PostgreSQL database dump complete
--

--
-- Database "template_postgis" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: template_postgis; Type: DATABASE; Schema: -; Owner: zenfleet_user
--

CREATE DATABASE template_postgis WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE template_postgis OWNER TO zenfleet_user;

\connect template_postgis

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: template_postgis; Type: DATABASE PROPERTIES; Schema: -; Owner: zenfleet_user
--

ALTER DATABASE template_postgis IS_TEMPLATE = true;
ALTER DATABASE template_postgis SET search_path TO '$user', 'public', 'topology', 'tiger';


\connect template_postgis

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: tiger; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA tiger;


ALTER SCHEMA tiger OWNER TO zenfleet_user;

--
-- Name: tiger_data; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA tiger_data;


ALTER SCHEMA tiger_data OWNER TO zenfleet_user;

--
-- Name: topology; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA topology;


ALTER SCHEMA topology OWNER TO zenfleet_user;

--
-- Name: SCHEMA topology; Type: COMMENT; Schema: -; Owner: zenfleet_user
--

COMMENT ON SCHEMA topology IS 'PostGIS Topology schema';


--
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA public;


--
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry and geography spatial types and functions';


--
-- Name: postgis_tiger_geocoder; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_tiger_geocoder WITH SCHEMA tiger;


--
-- Name: EXTENSION postgis_tiger_geocoder; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_tiger_geocoder IS 'PostGIS tiger geocoder and reverse geocoder';


--
-- Name: postgis_topology; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_topology WITH SCHEMA topology;


--
-- Name: EXTENSION postgis_topology; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_topology IS 'PostGIS topology spatial types and functions';


--
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.spatial_ref_sys (srid, auth_name, auth_srid, srtext, proj4text) FROM stdin;
\.


--
-- Data for Name: geocode_settings; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.geocode_settings (name, setting, unit, category, short_desc) FROM stdin;
\.


--
-- Data for Name: pagc_gaz; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_gaz (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_lex; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_lex (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_rules; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_rules (id, rule, is_custom) FROM stdin;
\.


--
-- Data for Name: topology; Type: TABLE DATA; Schema: topology; Owner: zenfleet_user
--

COPY topology.topology (id, name, srid, "precision", hasz) FROM stdin;
\.


--
-- Data for Name: layer; Type: TABLE DATA; Schema: topology; Owner: zenfleet_user
--

COPY topology.layer (topology_id, layer_id, schema_name, table_name, feature_column, feature_type, level, child_id) FROM stdin;
\.


--
-- Name: topology_id_seq; Type: SEQUENCE SET; Schema: topology; Owner: zenfleet_user
--

SELECT pg_catalog.setval('topology.topology_id_seq', 1, false);


--
-- PostgreSQL database dump complete
--

--
-- Database "zenfleet_db" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: zenfleet_db; Type: DATABASE; Schema: -; Owner: zenfleet_user
--

CREATE DATABASE zenfleet_db WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE zenfleet_db OWNER TO zenfleet_user;

\connect zenfleet_db

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: zenfleet_db; Type: DATABASE PROPERTIES; Schema: -; Owner: zenfleet_user
--

ALTER DATABASE zenfleet_db SET search_path TO '$user', 'public', 'topology', 'tiger';


\connect zenfleet_db

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: tiger; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA tiger;


ALTER SCHEMA tiger OWNER TO zenfleet_user;

--
-- Name: tiger_data; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA tiger_data;


ALTER SCHEMA tiger_data OWNER TO zenfleet_user;

--
-- Name: topology; Type: SCHEMA; Schema: -; Owner: zenfleet_user
--

CREATE SCHEMA topology;


ALTER SCHEMA topology OWNER TO zenfleet_user;

--
-- Name: SCHEMA topology; Type: COMMENT; Schema: -; Owner: zenfleet_user
--

COMMENT ON SCHEMA topology IS 'PostGIS Topology schema';


--
-- Name: btree_gist; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS btree_gist WITH SCHEMA public;


--
-- Name: EXTENSION btree_gist; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION btree_gist IS 'support for indexing common datatypes in GiST';


--
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA public;


--
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry and geography spatial types and functions';


--
-- Name: postgis_tiger_geocoder; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_tiger_geocoder WITH SCHEMA tiger;


--
-- Name: EXTENSION postgis_tiger_geocoder; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_tiger_geocoder IS 'PostGIS tiger geocoder and reverse geocoder';


--
-- Name: postgis_topology; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_topology WITH SCHEMA topology;


--
-- Name: EXTENSION postgis_topology; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_topology IS 'PostGIS topology spatial types and functions';


--
-- Name: budget_period_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.budget_period_enum AS ENUM (
    'monthly',
    'quarterly',
    'yearly'
);


ALTER TYPE public.budget_period_enum OWNER TO zenfleet_user;

--
-- Name: expense_category_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.expense_category_enum AS ENUM (
    'maintenance_preventive',
    'reparation',
    'pieces_detachees',
    'carburant',
    'assurance',
    'controle_technique',
    'vignette',
    'amendes',
    'peage',
    'parking',
    'lavage',
    'transport',
    'formation_chauffeur',
    'autre'
);


ALTER TYPE public.expense_category_enum OWNER TO zenfleet_user;

--
-- Name: manager_decision_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.manager_decision_enum AS ENUM (
    'valide',
    'refuse'
);


ALTER TYPE public.manager_decision_enum OWNER TO zenfleet_user;

--
-- Name: repair_priority_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.repair_priority_enum AS ENUM (
    'urgente',
    'a_prevoir',
    'non_urgente'
);


ALTER TYPE public.repair_priority_enum OWNER TO zenfleet_user;

--
-- Name: repair_status_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.repair_status_enum AS ENUM (
    'en_attente',
    'accord_initial',
    'accordee',
    'refusee',
    'en_cours',
    'terminee',
    'annulee'
);


ALTER TYPE public.repair_status_enum OWNER TO zenfleet_user;

--
-- Name: supervisor_decision_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.supervisor_decision_enum AS ENUM (
    'accepte',
    'refuse'
);


ALTER TYPE public.supervisor_decision_enum OWNER TO zenfleet_user;

--
-- Name: supplier_type_enum; Type: TYPE; Schema: public; Owner: zenfleet_user
--

CREATE TYPE public.supplier_type_enum AS ENUM (
    'mecanicien',
    'assureur',
    'station_service',
    'pieces_detachees',
    'peinture_carrosserie',
    'pneumatiques',
    'electricite_auto',
    'controle_technique',
    'transport_vehicules',
    'autre'
);


ALTER TYPE public.supplier_type_enum OWNER TO zenfleet_user;

--
-- Name: assignment_computed_status(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.assignment_computed_status(start_dt timestamp without time zone, end_dt timestamp without time zone) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
            BEGIN
                IF start_dt > NOW() THEN
                    RETURN 'scheduled';
                ELSIF end_dt IS NULL OR end_dt > NOW() THEN
                    RETURN 'active';
                ELSE
                    RETURN 'completed';
                END IF;
            END;
            $$;


ALTER FUNCTION public.assignment_computed_status(start_dt timestamp without time zone, end_dt timestamp without time zone) OWNER TO zenfleet_user;

--
-- Name: assignment_interval(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.assignment_interval(start_dt timestamp without time zone, end_dt timestamp without time zone) RETURNS tstzrange
    LANGUAGE plpgsql IMMUTABLE
    AS $$
            BEGIN
                -- Si end_dt est NULL, utiliser une date très future (2099-12-31)
                IF end_dt IS NULL THEN
                    RETURN tstzrange(start_dt, '2099-12-31 23:59:59'::timestamp);
                ELSE
                    RETURN tstzrange(start_dt, end_dt);
                END IF;
            END;
            $$;


ALTER FUNCTION public.assignment_interval(start_dt timestamp without time zone, end_dt timestamp without time zone) OWNER TO zenfleet_user;

--
-- Name: audit_cleanup_old_partitions(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.audit_cleanup_old_partitions() RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
                retention_months INTEGER;
                cutoff_date DATE;
                partition_name TEXT;
            BEGIN
                -- Récupère la rétention depuis organizations (par défaut 24 mois)
                SELECT COALESCE(MIN(data_retention_period), 24) INTO retention_months
                FROM organizations WHERE data_retention_period IS NOT NULL;

                cutoff_date := CURRENT_DATE - (retention_months || ' months')::INTERVAL;

                -- Supprime les partitions trop anciennes
                FOR partition_name IN
                    SELECT schemaname||'.'||tablename
                    FROM pg_tables
                    WHERE tablename LIKE 'audit_logs_%'
                    AND tablename < 'audit_logs_' || to_char(cutoff_date, 'YYYY_MM')
                LOOP
                    EXECUTE 'DROP TABLE IF EXISTS ' || partition_name || ' CASCADE';
                    RAISE NOTICE 'Dropped partition: %', partition_name;
                END LOOP;
            END;
            $$;


ALTER FUNCTION public.audit_cleanup_old_partitions() OWNER TO zenfleet_user;

--
-- Name: audit_create_monthly_partition(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.audit_create_monthly_partition() RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
                next_month DATE;
                partition_name TEXT;
                start_date TEXT;
                end_date TEXT;
                sql_command TEXT;
            BEGIN
                next_month := DATE_TRUNC('month', CURRENT_DATE + INTERVAL '2 months');
                partition_name := 'audit_logs_' || to_char(next_month, 'YYYY_MM');
                start_date := to_char(next_month, 'YYYY-MM-DD');
                end_date := to_char(next_month + INTERVAL '1 month', 'YYYY-MM-DD');

                -- Vérifie si la partition existe déjà
                IF NOT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = partition_name) THEN
                    sql_command := 'CREATE TABLE ' || partition_name ||
                                  ' PARTITION OF comprehensive_audit_logs FOR VALUES FROM (''' ||
                                  start_date || ''') TO (''' || end_date || ''')';
                    EXECUTE sql_command;
                    RAISE NOTICE 'Created partition: %', partition_name;
                END IF;
            END;
            $$;


ALTER FUNCTION public.audit_create_monthly_partition() OWNER TO zenfleet_user;

--
-- Name: basic_system_cleanup(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.basic_system_cleanup() RETURNS void
    LANGUAGE plpgsql
    AS $$
            BEGIN
                -- Nettoie les sessions expirées (plus de 7 jours)
                DELETE FROM sessions WHERE last_activity < EXTRACT(EPOCH FROM NOW() - INTERVAL '7 days');

                -- Nettoie les tokens expirés
                DELETE FROM personal_access_tokens WHERE expires_at IS NOT NULL AND expires_at < NOW();

                RAISE NOTICE 'Basic system cleanup completed at %', NOW();
            END;
            $$;


ALTER FUNCTION public.basic_system_cleanup() OWNER TO zenfleet_user;

--
-- Name: calculate_daily_metrics(date); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.calculate_daily_metrics(p_date date DEFAULT CURRENT_DATE) RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
                org_record RECORD;
                stats RECORD;
            BEGIN
                FOR org_record IN SELECT id FROM organizations WHERE status = 'active'
                LOOP
                    -- Calcule les stats pour cette organisation
                    SELECT * INTO stats FROM get_vehicle_stats(org_record.id);

                    -- Insert ou update métriques
                    INSERT INTO daily_metrics (
                        metric_date, organization_id, total_vehicles, active_vehicles,
                        total_drivers, active_drivers, daily_assignments
                    ) VALUES (
                        p_date, org_record.id, stats.total_vehicles, stats.active_vehicles,
                        stats.total_drivers, stats.active_drivers, stats.active_assignments
                    )
                    ON CONFLICT (metric_date, organization_id)
                    DO UPDATE SET
                        total_vehicles = EXCLUDED.total_vehicles,
                        active_vehicles = EXCLUDED.active_vehicles,
                        total_drivers = EXCLUDED.total_drivers,
                        active_drivers = EXCLUDED.active_drivers,
                        daily_assignments = EXCLUDED.daily_assignments,
                        updated_at = NOW();
                END LOOP;

                RAISE NOTICE 'Daily metrics calculated for %', p_date;
            END;
            $$;


ALTER FUNCTION public.calculate_daily_metrics(p_date date) OWNER TO zenfleet_user;

--
-- Name: calculate_supplier_scores(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.calculate_supplier_scores() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            DECLARE
                v_quality_score DECIMAL(5,2);
                v_reliability_score DECIMAL(5,2);
                v_overall_rating DECIMAL(3,2);
                v_completion_rate DECIMAL(5,2);
                v_punctuality_rate DECIMAL(5,2);
                v_complaint_rate DECIMAL(5,2);
            BEGIN
                -- Calculer uniquement si auto_score_enabled = true
                IF NEW.auto_score_enabled = true THEN
                    
                    -- Calculer le taux de complétion
                    IF NEW.total_orders > 0 THEN
                        v_completion_rate := (NEW.completed_orders::DECIMAL / NEW.total_orders) * 100;
                    ELSE
                        v_completion_rate := 75.00; -- Valeur par défaut
                    END IF;
                    
                    -- Calculer le taux de ponctualité
                    IF NEW.completed_orders > 0 THEN
                        v_punctuality_rate := (NEW.on_time_deliveries::DECIMAL / NEW.completed_orders) * 100;
                    ELSE
                        v_punctuality_rate := 75.00; -- Valeur par défaut
                    END IF;
                    
                    -- Calculer le taux de réclamation (inversé)
                    IF NEW.total_orders > 0 THEN
                        v_complaint_rate := 100 - LEAST(100, (NEW.customer_complaints::DECIMAL / NEW.total_orders) * 100);
                    ELSE
                        v_complaint_rate := 95.00; -- Valeur par défaut (peu de plaintes)
                    END IF;
                    
                    -- Score de qualité: 50% taux complétion + 50% absence de plaintes
                    v_quality_score := (v_completion_rate * 0.5) + (v_complaint_rate * 0.5);
                    
                    -- Score de fiabilité: 70% ponctualité + 30% temps de réponse
                    IF NEW.avg_response_time_hours IS NOT NULL THEN
                        -- Bonus pour temps de réponse rapide (max 100 points si < 1h, min 0 si > 48h)
                        v_reliability_score := (v_punctuality_rate * 0.7) + 
                            (GREATEST(0, LEAST(100, (100 - (NEW.avg_response_time_hours * 2)))) * 0.3);
                    ELSE
                        v_reliability_score := v_punctuality_rate;
                    END IF;
                    
                    -- Rating global: moyenne pondérée (qualité 40%, fiabilité 60%)
                    v_overall_rating := ((v_quality_score * 0.4) + (v_reliability_score * 0.6)) / 20; -- Convertir 0-100 en 0-5
                    
                    -- Mettre à jour les scores
                    NEW.quality_score := ROUND(v_quality_score, 2);
                    NEW.reliability_score := ROUND(v_reliability_score, 2);
                    NEW.rating := ROUND(v_overall_rating, 2);
                    NEW.last_evaluation_date := CURRENT_TIMESTAMP;
                END IF;
                
                -- Si les scores sont NULL, appliquer les valeurs par défaut
                NEW.quality_score := COALESCE(NEW.quality_score, 75.00);
                NEW.reliability_score := COALESCE(NEW.reliability_score, 75.00);
                NEW.rating := COALESCE(NEW.rating, 3.75);
                
                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.calculate_supplier_scores() OWNER TO zenfleet_user;

--
-- Name: check_mileage_consistency(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.check_mileage_consistency() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            DECLARE
                last_mileage BIGINT;
            BEGIN
                -- Récupérer le dernier kilométrage enregistré pour ce véhicule
                SELECT mileage INTO last_mileage
                FROM vehicle_mileage_readings
                WHERE vehicle_id = NEW.vehicle_id
                  AND recorded_at < NEW.recorded_at
                ORDER BY recorded_at DESC
                LIMIT 1;

                -- Si un relevé précédent existe et que le nouveau kilométrage est inférieur
                -- (en dehors des corrections manuelles), lever une exception
                IF last_mileage IS NOT NULL AND NEW.mileage < last_mileage AND NEW.recording_method = 'automatic' THEN
                    RAISE EXCEPTION 'Mileage consistency error: New mileage (%) is less than previous mileage (%) for vehicle_id %',
                        NEW.mileage, last_mileage, NEW.vehicle_id;
                END IF;

                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.check_mileage_consistency() OWNER TO zenfleet_user;

--
-- Name: detect_expense_anomalies(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.detect_expense_anomalies() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                -- Détection d'anomalie: montant très élevé
                IF NEW.total_ttc > 1000000 THEN
                    UPDATE expense_audit_logs
                    SET is_anomaly = true,
                        anomaly_details = 'Montant très élevé: ' || NEW.total_ttc,
                        risk_level = 'high',
                        requires_review = true
                    WHERE vehicle_expense_id = NEW.id
                    AND created_at = (
                        SELECT MAX(created_at) 
                        FROM expense_audit_logs 
                        WHERE vehicle_expense_id = NEW.id
                    );
                END IF;
                
                -- Détection: approbation trop rapide
                IF NEW.approval_status = 'approved' 
                   AND NEW.created_at > NOW() - INTERVAL '5 minutes' THEN
                    UPDATE expense_audit_logs
                    SET is_anomaly = true,
                        anomaly_details = 'Approbation très rapide (moins de 5 minutes)',
                        risk_level = 'medium',
                        requires_review = true
                    WHERE id = (
                        SELECT id
                        FROM expense_audit_logs
                        WHERE vehicle_expense_id = NEW.id
                        AND action = 'approved'
                        ORDER BY created_at DESC
                        LIMIT 1
                    );
                END IF;
                
                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.detect_expense_anomalies() OWNER TO zenfleet_user;

--
-- Name: get_user_accessible_organizations(bigint); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.get_user_accessible_organizations(p_user_id bigint) RETURNS TABLE(org_id bigint, org_name text, role_name text, is_primary boolean)
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    uo.organization_id,
                    o.name::TEXT,
                    uo.role::TEXT,
                    uo.is_primary
                FROM user_organizations uo
                JOIN organizations o ON uo.organization_id = o.id
                WHERE uo.user_id = p_user_id
                AND uo.is_active = true
                AND (uo.expires_at IS NULL OR uo.expires_at > NOW())
                AND o.status = 'active'
                ORDER BY uo.is_primary DESC, o.name;
            END;
            $$;


ALTER FUNCTION public.get_user_accessible_organizations(p_user_id bigint) OWNER TO zenfleet_user;

--
-- Name: get_vehicle_stats(bigint); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.get_vehicle_stats(p_organization_id bigint DEFAULT NULL::bigint) RETURNS TABLE(total_vehicles integer, active_vehicles integer, total_drivers integer, active_drivers integer, active_assignments integer)
    LANGUAGE plpgsql
    AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    COUNT(DISTINCT v.id)::INTEGER as total_vehicles,
                    COUNT(DISTINCT v.id) FILTER (WHERE vs.status = 'active')::INTEGER as active_vehicles,
                    COUNT(DISTINCT d.id)::INTEGER as total_drivers,
                    COUNT(DISTINCT d.id) FILTER (WHERE ds.status = 'active')::INTEGER as active_drivers,
                    COUNT(DISTINCT a.id) FILTER (WHERE a.end_datetime IS NULL)::INTEGER as active_assignments
                FROM organizations o
                LEFT JOIN vehicles v ON o.id = v.organization_id AND v.deleted_at IS NULL
                LEFT JOIN vehicle_statuses vs ON v.status_id = vs.id
                LEFT JOIN drivers d ON o.id = d.organization_id AND d.deleted_at IS NULL
                LEFT JOIN driver_statuses ds ON d.status_id = ds.id
                LEFT JOIN assignments a ON v.id = a.vehicle_id AND a.deleted_at IS NULL
                WHERE (p_organization_id IS NULL OR o.id = p_organization_id)
                AND o.status = 'active';
            END;
            $$;


ALTER FUNCTION public.get_vehicle_stats(p_organization_id bigint) OWNER TO zenfleet_user;

--
-- Name: log_expense_changes(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.log_expense_changes() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            DECLARE
                v_action TEXT;
                v_old_values JSONB;
                v_new_values JSONB;
                v_changed_fields JSONB;
                v_user_id BIGINT;
            BEGIN
                -- Déterminer l'action
                IF TG_OP = 'INSERT' THEN
                    v_action := 'created';
                    v_old_values := NULL;
                    v_new_values := to_jsonb(NEW);
                ELSIF TG_OP = 'UPDATE' THEN
                    v_action := 'updated';
                    v_old_values := to_jsonb(OLD);
                    v_new_values := to_jsonb(NEW);
                    
                    -- Détection d'actions spécifiques
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
                
                -- Obtenir l'ID utilisateur (à améliorer avec contexte session)
                v_user_id := COALESCE(NEW.updated_by, NEW.recorded_by, OLD.updated_by, OLD.recorded_by);
                
                -- Calculer les champs modifiés
                IF TG_OP = 'UPDATE' THEN
                    SELECT jsonb_agg(key)
                    INTO v_changed_fields
                    FROM jsonb_each(v_old_values)
                    WHERE v_old_values->key IS DISTINCT FROM v_new_values->key;
                ELSE
                    v_changed_fields := '[]'::jsonb;
                END IF;
                
                -- Insérer le log
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
            $$;


ALTER FUNCTION public.log_expense_changes() OWNER TO zenfleet_user;

--
-- Name: refresh_assignment_stats(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.refresh_assignment_stats() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                REFRESH MATERIALIZED VIEW CONCURRENTLY assignment_stats_daily;
                RETURN NULL;
            END;
            $$;


ALTER FUNCTION public.refresh_assignment_stats() OWNER TO zenfleet_user;

--
-- Name: update_approval_status(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.update_approval_status() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF NEW.is_rejected THEN
                    NEW.approval_status = 'rejected';
                ELSIF NEW.level2_approved THEN
                    NEW.approval_status = 'approved';
                    NEW.approved = true;
                ELSIF NEW.level1_approved THEN
                    NEW.approval_status = 'pending_level2';
                ELSIF NEW.needs_approval THEN
                    NEW.approval_status = 'pending_level1';
                ELSE
                    NEW.approval_status = 'draft';
                END IF;
                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.update_approval_status() OWNER TO zenfleet_user;

--
-- Name: update_expense_group_budget(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.update_expense_group_budget() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
                    UPDATE expense_groups
                    SET budget_used = (
                        SELECT COALESCE(SUM(total_ttc), 0)
                        FROM vehicle_expenses
                        WHERE expense_group_id = COALESCE(NEW.expense_group_id, OLD.expense_group_id)
                        AND deleted_at IS NULL
                    )
                    WHERE id = COALESCE(NEW.expense_group_id, OLD.expense_group_id);
                END IF;
                
                IF TG_OP = 'DELETE' THEN
                    UPDATE expense_groups
                    SET budget_used = (
                        SELECT COALESCE(SUM(total_ttc), 0)
                        FROM vehicle_expenses
                        WHERE expense_group_id = OLD.expense_group_id
                        AND deleted_at IS NULL
                    )
                    WHERE id = OLD.expense_group_id;
                END IF;
                
                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.update_expense_group_budget() OWNER TO zenfleet_user;

--
-- Name: update_organization_hierarchy(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.update_organization_hierarchy() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND OLD.parent_organization_id != NEW.parent_organization_id) THEN
                    -- Calcul du niveau et du chemin
                    IF NEW.parent_organization_id IS NULL THEN
                        NEW.hierarchy_depth := 0;
                        NEW.hierarchy_path := '/' || NEW.id || '/';
                    ELSE
                        SELECT hierarchy_depth + 1, hierarchy_path || NEW.id || '/'
                        INTO NEW.hierarchy_depth, NEW.hierarchy_path
                        FROM organizations
                        WHERE id = NEW.parent_organization_id;
                    END IF;

                    -- Validation profondeur maximale
                    IF NEW.hierarchy_depth > 5 THEN
                        RAISE EXCEPTION 'Profondeur hiérarchique maximale dépassée (5 niveaux)';
                    END IF;

                    -- Prévention cycle
                    IF NEW.hierarchy_path LIKE '%/' || NEW.parent_organization_id || '/%' THEN
                        RAISE EXCEPTION 'Référence circulaire détectée dans la hiérarchie';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.update_organization_hierarchy() OWNER TO zenfleet_user;

--
-- Name: user_has_permission(bigint, bigint, text); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.user_has_permission(p_user_id bigint, p_organization_id bigint, p_permission text) RETURNS boolean
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
            DECLARE
                has_permission BOOLEAN := FALSE;
            BEGIN
                -- Vérifie permissions directes
                SELECT EXISTS (
                    SELECT 1 FROM contextual_permissions cp
                    WHERE cp.user_id = p_user_id
                    AND cp.organization_id = p_organization_id
                    AND cp.permission_name = p_permission
                    AND cp.valid_from <= NOW()
                    AND (cp.valid_until IS NULL OR cp.valid_until > NOW())
                ) INTO has_permission;

                -- Vérifie via rôles Spatie si pas de permission directe
                IF NOT has_permission THEN
                    SELECT EXISTS (
                        SELECT 1 FROM users u
                        JOIN model_has_permissions mhp ON u.id = mhp.model_id
                        JOIN permissions p ON mhp.permission_id = p.id
                        WHERE u.id = p_user_id
                        AND p.name = p_permission
                        AND mhp.model_type = 'App\\Models\\User'
                    ) INTO has_permission;
                END IF;

                RETURN has_permission;
            END;
            $$;


ALTER FUNCTION public.user_has_permission(p_user_id bigint, p_organization_id bigint, p_permission text) OWNER TO zenfleet_user;

--
-- Name: validate_assignment_business_rules(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.validate_assignment_business_rules() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                -- Validation: end_datetime > start_datetime
                IF NEW.end_datetime IS NOT NULL AND NEW.end_datetime <= NEW.start_datetime THEN
                    RAISE EXCEPTION 'Date de fin doit etre posterieure a date de debut';
                END IF;

                -- Validation: pas affectation dans le futur lointain
                IF NEW.start_datetime > NOW() + INTERVAL '1 year' THEN
                    RAISE EXCEPTION 'Impossible de creer affectation plus un an dans le futur';
                END IF;

                -- Validation: vehicule et chauffeur dans la meme organisation
                IF NEW.driver_id IS NOT NULL THEN
                    IF NOT EXISTS (
                        SELECT 1 FROM drivers d
                        JOIN vehicles v ON v.organization_id = d.organization_id
                        WHERE d.id = NEW.driver_id
                        AND v.id = NEW.vehicle_id
                        AND d.organization_id = NEW.organization_id
                    ) THEN
                        RAISE EXCEPTION 'Vehicule et chauffeur doivent appartenir a la meme organisation';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.validate_assignment_business_rules() OWNER TO zenfleet_user;

--
-- Name: validate_assignment_mileage(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.validate_assignment_mileage() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                -- Validation kilometrage croissant
                IF NEW.start_mileage IS NOT NULL AND NEW.end_mileage IS NOT NULL THEN
                    IF NEW.end_mileage < NEW.start_mileage THEN
                        RAISE EXCEPTION 'Kilometrage de fin ne peut pas etre inferieur au kilometrage de debut';
                    END IF;
                END IF;

                -- Validation coherence avec vehicule
                IF NEW.start_mileage IS NOT NULL THEN
                    IF EXISTS (
                        SELECT 1 FROM vehicles v
                        WHERE v.id = NEW.vehicle_id
                        AND v.current_mileage > NEW.start_mileage + 10000 -- Tolerance 10k km
                    ) THEN
                        RAISE EXCEPTION 'Kilometrage de debut incoherent avec kilometrage actuel du vehicule';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.validate_assignment_mileage() OWNER TO zenfleet_user;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: algeria_communes; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.algeria_communes (
    id bigint NOT NULL,
    wilaya_code character varying(2) NOT NULL,
    name_ar character varying(255),
    name_fr character varying(255) NOT NULL,
    postal_code character varying(5),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.algeria_communes OWNER TO zenfleet_user;

--
-- Name: algeria_communes_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.algeria_communes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.algeria_communes_id_seq OWNER TO zenfleet_user;

--
-- Name: algeria_communes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.algeria_communes_id_seq OWNED BY public.algeria_communes.id;


--
-- Name: algeria_wilayas; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.algeria_wilayas (
    code character varying(2) NOT NULL,
    name_ar character varying(255),
    name_fr character varying(255) NOT NULL,
    name_en character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.algeria_wilayas OWNER TO zenfleet_user;

--
-- Name: assignments; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.assignments (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    driver_id bigint NOT NULL,
    start_datetime timestamp(0) without time zone NOT NULL,
    end_datetime timestamp(0) without time zone,
    start_mileage bigint,
    end_mileage bigint,
    reason text,
    notes text,
    created_by_user_id bigint,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint,
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    created_by bigint,
    updated_by bigint,
    ended_by_user_id bigint,
    ended_at timestamp(0) without time zone
);


ALTER TABLE public.assignments OWNER TO zenfleet_user;

--
-- Name: assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.assignments_id_seq OWNER TO zenfleet_user;

--
-- Name: assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.assignments_id_seq OWNED BY public.assignments.id;


--
-- Name: comprehensive_audit_logs; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.comprehensive_audit_logs (
    id bigint NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
)
PARTITION BY RANGE (occurred_at);


ALTER TABLE public.comprehensive_audit_logs OWNER TO zenfleet_user;

--
-- Name: comprehensive_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.comprehensive_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comprehensive_audit_logs_id_seq OWNER TO zenfleet_user;

--
-- Name: comprehensive_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.comprehensive_audit_logs_id_seq OWNED BY public.comprehensive_audit_logs.id;


--
-- Name: audit_logs_2025_04; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_04 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_04 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_05; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_05 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_05 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_06; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_06 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_06 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_07; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_07 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_07 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_08; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_08 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_08 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_09; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_09 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_09 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_10; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_10 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_10 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_11; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_11 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_11 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2025_12; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2025_12 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2025_12 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2026_01; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2026_01 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2026_01 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2026_02; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2026_02 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2026_02 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2026_03; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2026_03 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2026_03 OWNER TO zenfleet_user;

--
-- Name: audit_logs_2026_04; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.audit_logs_2026_04 (
    id bigint DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass) NOT NULL,
    uuid uuid DEFAULT gen_random_uuid(),
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(50) NOT NULL,
    event_type character varying(50) NOT NULL,
    event_action character varying(50) NOT NULL,
    resource_type character varying(100),
    resource_id bigint,
    resource_identifier character varying(255),
    old_values jsonb,
    new_values jsonb,
    changes_summary text,
    ip_address inet,
    user_agent text,
    request_id uuid,
    session_id character varying(255),
    business_context jsonb,
    risk_level character varying(20) DEFAULT 'low'::character varying,
    compliance_tags text[],
    occurred_at timestamp with time zone DEFAULT now() NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chk_event_category CHECK (((event_category)::text = ANY ((ARRAY['authentication'::character varying, 'authorization'::character varying, 'data_access'::character varying, 'data_modification'::character varying, 'system_configuration'::character varying, 'user_management'::character varying, 'fleet_operations'::character varying, 'financial'::character varying, 'maintenance'::character varying, 'compliance'::character varying, 'security'::character varying, 'integration'::character varying])::text[]))),
    CONSTRAINT chk_risk_level CHECK (((risk_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.audit_logs_2026_04 OWNER TO zenfleet_user;

--
-- Name: contextual_permissions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.contextual_permissions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    organization_id bigint NOT NULL,
    permission_scope_id bigint NOT NULL,
    permission_name character varying(255) NOT NULL,
    context_filters json,
    valid_from timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    valid_until timestamp(0) without time zone,
    granted_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.contextual_permissions OWNER TO zenfleet_user;

--
-- Name: contextual_permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.contextual_permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.contextual_permissions_id_seq OWNER TO zenfleet_user;

--
-- Name: contextual_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.contextual_permissions_id_seq OWNED BY public.contextual_permissions.id;


--
-- Name: daily_metrics; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.daily_metrics (
    id bigint NOT NULL,
    metric_date date NOT NULL,
    organization_id bigint NOT NULL,
    total_vehicles integer DEFAULT 0 NOT NULL,
    active_vehicles integer DEFAULT 0 NOT NULL,
    total_drivers integer DEFAULT 0 NOT NULL,
    active_drivers integer DEFAULT 0 NOT NULL,
    daily_assignments integer DEFAULT 0 NOT NULL,
    total_mileage numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    daily_maintenance_cost numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    daily_fuel_cost numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.daily_metrics OWNER TO zenfleet_user;

--
-- Name: daily_metrics_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.daily_metrics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.daily_metrics_id_seq OWNER TO zenfleet_user;

--
-- Name: daily_metrics_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.daily_metrics_id_seq OWNED BY public.daily_metrics.id;


--
-- Name: depot_assignment_history; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.depot_assignment_history (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    depot_id bigint,
    organization_id bigint NOT NULL,
    previous_depot_id bigint,
    action character varying(20) NOT NULL,
    assigned_by bigint,
    notes text,
    assigned_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.depot_assignment_history OWNER TO zenfleet_user;

--
-- Name: depot_assignment_history_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.depot_assignment_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.depot_assignment_history_id_seq OWNER TO zenfleet_user;

--
-- Name: depot_assignment_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.depot_assignment_history_id_seq OWNED BY public.depot_assignment_history.id;


--
-- Name: document_categories; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.document_categories (
    id bigint NOT NULL,
    organization_id bigint,
    name character varying(255) NOT NULL,
    description text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_default boolean DEFAULT false NOT NULL,
    meta_schema json,
    slug character varying(100)
);


ALTER TABLE public.document_categories OWNER TO zenfleet_user;

--
-- Name: document_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.document_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.document_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: document_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.document_categories_id_seq OWNED BY public.document_categories.id;


--
-- Name: document_revisions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.document_revisions (
    id bigint NOT NULL,
    document_id bigint NOT NULL,
    user_id bigint NOT NULL,
    file_path character varying(255) NOT NULL,
    original_filename character varying(255) NOT NULL,
    mime_type character varying(255) NOT NULL,
    size_in_bytes bigint NOT NULL,
    extra_metadata jsonb,
    description text,
    issue_date date,
    expiry_date date,
    revision_number integer NOT NULL,
    revision_notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.document_revisions OWNER TO zenfleet_user;

--
-- Name: COLUMN document_revisions.user_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.document_revisions.user_id IS 'User who created this revision';


--
-- Name: COLUMN document_revisions.revision_notes; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.document_revisions.revision_notes IS 'Why this revision was created';


--
-- Name: document_revisions_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.document_revisions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.document_revisions_id_seq OWNER TO zenfleet_user;

--
-- Name: document_revisions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.document_revisions_id_seq OWNED BY public.document_revisions.id;


--
-- Name: documentables; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.documentables (
    document_id bigint NOT NULL,
    documentable_type character varying(255) NOT NULL,
    documentable_id bigint NOT NULL
);


ALTER TABLE public.documentables OWNER TO zenfleet_user;

--
-- Name: documents; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.documents (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    organization_id bigint NOT NULL,
    document_category_id bigint NOT NULL,
    user_id bigint NOT NULL,
    file_path character varying(255) NOT NULL,
    original_filename character varying(255) NOT NULL,
    mime_type character varying(255) NOT NULL,
    size_in_bytes bigint NOT NULL,
    issue_date date,
    expiry_date date,
    description text,
    extra_metadata jsonb,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'validated'::character varying NOT NULL,
    is_latest_version boolean DEFAULT true NOT NULL,
    search_vector tsvector GENERATED ALWAYS AS (((setweight(to_tsvector('french'::regconfig, (COALESCE(original_filename, ''::character varying))::text), 'A'::"char") || setweight(to_tsvector('french'::regconfig, COALESCE(description, ''::text)), 'B'::"char")) || setweight(to_tsvector('french'::regconfig, COALESCE((extra_metadata)::text, ''::text)), 'C'::"char"))) STORED
);


ALTER TABLE public.documents OWNER TO zenfleet_user;

--
-- Name: COLUMN documents.user_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.documents.user_id IS 'User who uploaded the document';


--
-- Name: documents_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.documents_id_seq OWNER TO zenfleet_user;

--
-- Name: documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.documents_id_seq OWNED BY public.documents.id;


--
-- Name: driver_sanction_histories; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.driver_sanction_histories (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    sanction_id bigint NOT NULL,
    user_id bigint NOT NULL,
    action character varying(255) NOT NULL,
    details json NOT NULL,
    ip_address character varying(45),
    user_agent character varying(500),
    CONSTRAINT driver_sanction_histories_action_check CHECK (((action)::text = ANY ((ARRAY['created'::character varying, 'updated'::character varying, 'archived'::character varying, 'unarchived'::character varying, 'deleted'::character varying])::text[])))
);


ALTER TABLE public.driver_sanction_histories OWNER TO zenfleet_user;

--
-- Name: COLUMN driver_sanction_histories.action; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanction_histories.action IS 'Type d''action effectuée sur la sanction';


--
-- Name: COLUMN driver_sanction_histories.details; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanction_histories.details IS 'Détails de l''action (changements effectués, valeurs avant/après)';


--
-- Name: COLUMN driver_sanction_histories.ip_address; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanction_histories.ip_address IS 'Adresse IP de l''utilisateur lors de l''action';


--
-- Name: COLUMN driver_sanction_histories.user_agent; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanction_histories.user_agent IS 'User agent du navigateur';


--
-- Name: driver_sanction_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.driver_sanction_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.driver_sanction_histories_id_seq OWNER TO zenfleet_user;

--
-- Name: driver_sanction_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.driver_sanction_histories_id_seq OWNED BY public.driver_sanction_histories.id;


--
-- Name: driver_sanctions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.driver_sanctions (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    organization_id bigint NOT NULL,
    driver_id bigint NOT NULL,
    supervisor_id bigint NOT NULL,
    sanction_type character varying(255) NOT NULL,
    reason text NOT NULL,
    sanction_date date NOT NULL,
    attachment_path character varying(500),
    archived_at timestamp(0) without time zone,
    severity character varying(20) DEFAULT 'medium'::character varying NOT NULL,
    duration_days integer,
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    notes text,
    CONSTRAINT driver_sanctions_sanction_type_check CHECK (((sanction_type)::text = ANY ((ARRAY['avertissement_verbal'::character varying, 'avertissement_ecrit'::character varying, 'mise_a_pied'::character varying, 'mise_en_demeure'::character varying, 'suspension_permis'::character varying, 'amende'::character varying, 'blame'::character varying, 'licenciement'::character varying])::text[]))),
    CONSTRAINT driver_sanctions_severity_check CHECK (((severity)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[]))),
    CONSTRAINT driver_sanctions_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'appealed'::character varying, 'cancelled'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.driver_sanctions OWNER TO zenfleet_user;

--
-- Name: COLUMN driver_sanctions.sanction_type; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.sanction_type IS 'Type de sanction disciplinaire';


--
-- Name: COLUMN driver_sanctions.reason; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.reason IS 'Description détaillée des faits ayant conduit à la sanction';


--
-- Name: COLUMN driver_sanctions.sanction_date; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.sanction_date IS 'Date à laquelle la sanction a été prononcée';


--
-- Name: COLUMN driver_sanctions.attachment_path; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.attachment_path IS 'Chemin vers le document attaché (lettre officielle, PV, etc.)';


--
-- Name: COLUMN driver_sanctions.archived_at; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.archived_at IS 'Date d''archivage de la sanction (pour historique)';


--
-- Name: COLUMN driver_sanctions.severity; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.severity IS 'Niveau de gravité: low, medium, high, critical';


--
-- Name: COLUMN driver_sanctions.duration_days; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.duration_days IS 'Durée de la sanction en jours (pour mise à pied, suspension, etc.)';


--
-- Name: COLUMN driver_sanctions.status; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.status IS 'Statut: active, appealed, cancelled, archived';


--
-- Name: COLUMN driver_sanctions.notes; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.driver_sanctions.notes IS 'Notes additionnelles sur la sanction';


--
-- Name: driver_sanctions_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.driver_sanctions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.driver_sanctions_id_seq OWNER TO zenfleet_user;

--
-- Name: driver_sanctions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.driver_sanctions_id_seq OWNED BY public.driver_sanctions.id;


--
-- Name: driver_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.driver_statuses (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    description text,
    color character varying(20) DEFAULT 'blue'::character varying NOT NULL,
    text_color character varying(20) DEFAULT 'white'::character varying NOT NULL,
    icon character varying(50),
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    allows_assignments boolean DEFAULT true NOT NULL,
    is_available_for_work boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    priority_level smallint DEFAULT '1'::smallint NOT NULL,
    organization_id bigint,
    metadata json,
    valid_from timestamp(0) without time zone,
    valid_until timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    can_drive boolean DEFAULT true NOT NULL,
    can_assign boolean DEFAULT true NOT NULL,
    requires_validation boolean DEFAULT false NOT NULL
);


ALTER TABLE public.driver_statuses OWNER TO zenfleet_user;

--
-- Name: driver_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.driver_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.driver_statuses_id_seq OWNER TO zenfleet_user;

--
-- Name: driver_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.driver_statuses_id_seq OWNED BY public.driver_statuses.id;


--
-- Name: drivers; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.drivers (
    id bigint NOT NULL,
    user_id bigint,
    employee_number character varying(100),
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    photo character varying(512),
    birth_date date,
    blood_type character varying(10),
    address text,
    personal_phone character varying(50),
    personal_email character varying(255),
    license_number character varying(100),
    license_issue_date date,
    license_authority character varying(255),
    recruitment_date date,
    contract_end_date date,
    status_id bigint,
    emergency_contact_name character varying(255),
    emergency_contact_phone character varying(50),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    full_address text,
    license_expiry_date date,
    organization_id bigint,
    supervisor_id bigint,
    license_categories json,
    emergency_contact_relationship character varying(100),
    notes text
);


ALTER TABLE public.drivers OWNER TO zenfleet_user;

--
-- Name: TABLE drivers; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON TABLE public.drivers IS 'Chauffeurs - Table enterprise avec colonnes étendues';


--
-- Name: COLUMN drivers.license_categories; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.drivers.license_categories IS 'Catégories de permis (JSON array: ["B", "C", "D", etc.])';


--
-- Name: COLUMN drivers.emergency_contact_relationship; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.drivers.emergency_contact_relationship IS 'Lien de parenté avec le contact d''urgence';


--
-- Name: COLUMN drivers.notes; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.drivers.notes IS 'Notes professionnelles, compétences, formations, remarques';


--
-- Name: drivers_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.drivers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.drivers_id_seq OWNER TO zenfleet_user;

--
-- Name: drivers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.drivers_id_seq OWNED BY public.drivers.id;


--
-- Name: expense_audit_logs; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.expense_audit_logs (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_expense_id bigint NOT NULL,
    user_id bigint NOT NULL,
    action character varying(50) NOT NULL,
    action_category character varying(50) NOT NULL,
    description text NOT NULL,
    old_values json,
    new_values json,
    changed_fields json DEFAULT '[]'::json NOT NULL,
    ip_address character varying(45),
    user_agent character varying(255),
    session_id character varying(255),
    request_id character varying(255),
    previous_status character varying(50),
    new_status character varying(50),
    previous_amount numeric(15,2),
    new_amount numeric(15,2),
    is_sensitive boolean DEFAULT false NOT NULL,
    requires_review boolean DEFAULT false NOT NULL,
    reviewed boolean DEFAULT false NOT NULL,
    reviewed_by bigint,
    reviewed_at timestamp(0) without time zone,
    review_notes text,
    is_anomaly boolean DEFAULT false NOT NULL,
    anomaly_details text,
    risk_level character varying(20),
    metadata json DEFAULT '{}'::json NOT NULL,
    tags json DEFAULT '[]'::json NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.expense_audit_logs OWNER TO zenfleet_user;

--
-- Name: expense_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.expense_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.expense_audit_logs_id_seq OWNER TO zenfleet_user;

--
-- Name: expense_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.expense_audit_logs_id_seq OWNED BY public.expense_audit_logs.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint,
    deleted_at timestamp(0) without time zone,
    first_name character varying(255),
    last_name character varying(255),
    phone character varying(50),
    supervisor_id bigint,
    manager_id bigint,
    is_super_admin boolean DEFAULT false NOT NULL,
    permissions_cache json,
    job_title character varying(255),
    department character varying(255),
    employee_id character varying(255),
    hire_date date,
    birth_date date,
    two_factor_enabled boolean DEFAULT false NOT NULL,
    failed_login_attempts integer DEFAULT 0 NOT NULL,
    locked_until timestamp(0) without time zone,
    password_changed_at timestamp(0) without time zone,
    last_activity_at timestamp(0) without time zone,
    last_login_at timestamp(0) without time zone,
    last_login_ip character varying(255),
    login_count integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    user_status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    timezone character varying(255) DEFAULT 'Europe/Paris'::character varying NOT NULL,
    language character varying(2) DEFAULT 'fr'::character varying NOT NULL,
    preferences json,
    notification_preferences json,
    role character varying(255) DEFAULT 'user'::character varying NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    CONSTRAINT users_user_status_check CHECK (((user_status)::text = ANY ((ARRAY['active'::character varying, 'inactive'::character varying, 'suspended'::character varying, 'pending'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO zenfleet_user;

--
-- Name: expense_audit_summary; Type: VIEW; Schema: public; Owner: zenfleet_user
--

CREATE VIEW public.expense_audit_summary AS
 SELECT eal.organization_id,
    eal.user_id,
    u.name AS user_name,
    eal.action,
    count(*) AS action_count,
    date(eal.created_at) AS action_date
   FROM (public.expense_audit_logs eal
     JOIN public.users u ON ((u.id = eal.user_id)))
  GROUP BY eal.organization_id, eal.user_id, u.name, eal.action, (date(eal.created_at));


ALTER VIEW public.expense_audit_summary OWNER TO zenfleet_user;

--
-- Name: expense_budgets; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.expense_budgets (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint,
    expense_category character varying(255),
    budget_period character varying(255) NOT NULL,
    budget_year integer NOT NULL,
    budget_month integer,
    budget_quarter integer,
    budgeted_amount numeric(15,2) NOT NULL,
    spent_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    remaining_amount numeric(15,2) GENERATED ALWAYS AS ((budgeted_amount - spent_amount)) STORED NOT NULL,
    variance_percentage numeric(5,2) GENERATED ALWAYS AS (
CASE
    WHEN (budgeted_amount > (0)::numeric) THEN (((spent_amount - budgeted_amount) / budgeted_amount) * (100)::numeric)
    ELSE (0)::numeric
END) STORED NOT NULL,
    warning_threshold numeric(5,2) DEFAULT '80'::numeric NOT NULL,
    critical_threshold numeric(5,2) DEFAULT '95'::numeric NOT NULL,
    description text,
    approval_workflow json DEFAULT '[]'::json NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT expense_budgets_budget_period_check CHECK (((budget_period)::text = ANY ((ARRAY['monthly'::character varying, 'quarterly'::character varying, 'yearly'::character varying])::text[]))),
    CONSTRAINT valid_budget_amounts CHECK (((budgeted_amount > (0)::numeric) AND (spent_amount >= (0)::numeric) AND (warning_threshold > (0)::numeric) AND (warning_threshold <= (100)::numeric) AND (critical_threshold > warning_threshold) AND (critical_threshold <= (100)::numeric))),
    CONSTRAINT valid_budget_period_data CHECK (((((budget_period)::text = 'monthly'::text) AND ((budget_month >= 1) AND (budget_month <= 12)) AND (budget_quarter IS NULL)) OR (((budget_period)::text = 'quarterly'::text) AND ((budget_quarter >= 1) AND (budget_quarter <= 4)) AND (budget_month IS NULL)) OR (((budget_period)::text = 'yearly'::text) AND (budget_month IS NULL) AND (budget_quarter IS NULL))))
);


ALTER TABLE public.expense_budgets OWNER TO zenfleet_user;

--
-- Name: expense_budgets_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.expense_budgets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.expense_budgets_id_seq OWNER TO zenfleet_user;

--
-- Name: expense_budgets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.expense_budgets_id_seq OWNED BY public.expense_budgets.id;


--
-- Name: expense_groups; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.expense_groups (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    budget_allocated numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    budget_used numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    budget_remaining numeric(15,2) GENERATED ALWAYS AS ((budget_allocated - budget_used)) STORED NOT NULL,
    fiscal_year integer DEFAULT 2025 NOT NULL,
    fiscal_quarter integer,
    fiscal_month integer,
    is_active boolean DEFAULT true NOT NULL,
    alert_on_threshold boolean DEFAULT true NOT NULL,
    alert_threshold_percentage numeric(5,2) DEFAULT '80'::numeric NOT NULL,
    block_on_exceeded boolean DEFAULT false NOT NULL,
    metadata json DEFAULT '{}'::json NOT NULL,
    tags json DEFAULT '[]'::json NOT NULL,
    responsible_users json DEFAULT '[]'::json NOT NULL,
    created_by bigint NOT NULL,
    updated_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT valid_alert_threshold CHECK (((alert_threshold_percentage >= (0)::numeric) AND (alert_threshold_percentage <= (100)::numeric))),
    CONSTRAINT valid_budget_amounts CHECK (((budget_allocated >= (0)::numeric) AND (budget_used >= (0)::numeric))),
    CONSTRAINT valid_fiscal_period CHECK ((((fiscal_year >= 2020) AND (fiscal_year <= 2100)) AND ((fiscal_quarter IS NULL) OR ((fiscal_quarter >= 1) AND (fiscal_quarter <= 4))) AND ((fiscal_month IS NULL) OR ((fiscal_month >= 1) AND (fiscal_month <= 12)))))
);


ALTER TABLE public.expense_groups OWNER TO zenfleet_user;

--
-- Name: COLUMN expense_groups.budget_used; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.expense_groups.budget_used IS 'Calculé automatiquement depuis vehicle_expenses';


--
-- Name: COLUMN expense_groups.budget_remaining; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.expense_groups.budget_remaining IS 'Budget restant calculé';


--
-- Name: expense_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.expense_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.expense_groups_id_seq OWNER TO zenfleet_user;

--
-- Name: expense_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.expense_groups_id_seq OWNED BY public.expense_groups.id;


--
-- Name: expense_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.expense_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    description text
);


ALTER TABLE public.expense_types OWNER TO zenfleet_user;

--
-- Name: expense_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.expense_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.expense_types_id_seq OWNER TO zenfleet_user;

--
-- Name: expense_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.expense_types_id_seq OWNED BY public.expense_types.id;


--
-- Name: expenses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.expenses (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint,
    driver_id bigint,
    expense_type_id bigint NOT NULL,
    amount numeric(12,2) NOT NULL,
    expense_date date NOT NULL,
    description text,
    receipt_path character varying(512),
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.expenses OWNER TO zenfleet_user;

--
-- Name: expenses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.expenses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.expenses_id_seq OWNER TO zenfleet_user;

--
-- Name: expenses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.expenses_id_seq OWNED BY public.expenses.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO zenfleet_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO zenfleet_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fuel_refills; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.fuel_refills (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    driver_id bigint,
    refill_date timestamp(0) without time zone NOT NULL,
    quantity_liters numeric(8,2) NOT NULL,
    price_per_liter numeric(8,3) NOT NULL,
    total_cost numeric(10,2) NOT NULL,
    mileage_at_refill bigint NOT NULL,
    full_tank boolean DEFAULT true NOT NULL,
    station_name character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.fuel_refills OWNER TO zenfleet_user;

--
-- Name: fuel_refills_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.fuel_refills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fuel_refills_id_seq OWNER TO zenfleet_user;

--
-- Name: fuel_refills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.fuel_refills_id_seq OWNED BY public.fuel_refills.id;


--
-- Name: fuel_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.fuel_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.fuel_types OWNER TO zenfleet_user;

--
-- Name: fuel_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.fuel_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fuel_types_id_seq OWNER TO zenfleet_user;

--
-- Name: fuel_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.fuel_types_id_seq OWNED BY public.fuel_types.id;


--
-- Name: granular_permissions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.granular_permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) DEFAULT 'web'::character varying NOT NULL,
    module character varying(255) NOT NULL,
    resource character varying(255) NOT NULL,
    action character varying(255) NOT NULL,
    scope character varying(255) DEFAULT 'organization'::character varying NOT NULL,
    description text,
    risk_level integer DEFAULT 1 NOT NULL,
    is_system boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT granular_permissions_scope_check CHECK (((scope)::text = ANY ((ARRAY['global'::character varying, 'organization'::character varying, 'supervised'::character varying, 'own'::character varying])::text[])))
);


ALTER TABLE public.granular_permissions OWNER TO zenfleet_user;

--
-- Name: granular_permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.granular_permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.granular_permissions_id_seq OWNER TO zenfleet_user;

--
-- Name: granular_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.granular_permissions_id_seq OWNED BY public.granular_permissions.id;


--
-- Name: incident_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.incident_statuses (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.incident_statuses OWNER TO zenfleet_user;

--
-- Name: incident_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.incident_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.incident_statuses_id_seq OWNER TO zenfleet_user;

--
-- Name: incident_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.incident_statuses_id_seq OWNED BY public.incident_statuses.id;


--
-- Name: incidents; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.incidents (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    driver_id bigint,
    incident_date timestamp(0) without time zone NOT NULL,
    type character varying(255) NOT NULL,
    severity character varying(255) NOT NULL,
    location text,
    description text NOT NULL,
    third_party_involved boolean DEFAULT false NOT NULL,
    police_report_number character varying(255),
    insurance_claim_number character varying(255),
    incident_status_id bigint NOT NULL,
    estimated_cost numeric(12,2),
    actual_cost numeric(12,2),
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.incidents OWNER TO zenfleet_user;

--
-- Name: incidents_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.incidents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.incidents_id_seq OWNER TO zenfleet_user;

--
-- Name: incidents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.incidents_id_seq OWNED BY public.incidents.id;


--
-- Name: maintenance_alerts; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_alerts (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    maintenance_schedule_id bigint NOT NULL,
    alert_type character varying(255) NOT NULL,
    priority character varying(255) DEFAULT 'medium'::character varying NOT NULL,
    message text NOT NULL,
    due_date date,
    due_mileage integer,
    is_acknowledged boolean DEFAULT false NOT NULL,
    acknowledged_by bigint,
    acknowledged_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT maintenance_alerts_alert_type_check CHECK (((alert_type)::text = ANY ((ARRAY['km_based'::character varying, 'time_based'::character varying, 'overdue'::character varying])::text[]))),
    CONSTRAINT maintenance_alerts_priority_check CHECK (((priority)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.maintenance_alerts OWNER TO zenfleet_user;

--
-- Name: maintenance_alerts_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_alerts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_alerts_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_alerts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_alerts_id_seq OWNED BY public.maintenance_alerts.id;


--
-- Name: maintenance_documents; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_documents (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    maintenance_operation_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    original_name character varying(255) NOT NULL,
    file_path character varying(500) NOT NULL,
    file_type character varying(50) NOT NULL,
    mime_type character varying(100) NOT NULL,
    file_size bigint NOT NULL,
    document_type character varying(255) NOT NULL,
    description text,
    metadata json,
    uploaded_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT maintenance_documents_document_type_check CHECK (((document_type)::text = ANY ((ARRAY['invoice'::character varying, 'report'::character varying, 'photo_before'::character varying, 'photo_after'::character varying, 'warranty'::character varying, 'other'::character varying])::text[])))
);


ALTER TABLE public.maintenance_documents OWNER TO zenfleet_user;

--
-- Name: maintenance_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_documents_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_documents_id_seq OWNED BY public.maintenance_documents.id;


--
-- Name: maintenance_logs; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_logs (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    maintenance_plan_id bigint,
    maintenance_type_id bigint NOT NULL,
    maintenance_status_id bigint NOT NULL,
    performed_on_date date NOT NULL,
    performed_at_mileage bigint NOT NULL,
    cost numeric(12,2),
    details text,
    performed_by character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint
);


ALTER TABLE public.maintenance_logs OWNER TO zenfleet_user;

--
-- Name: maintenance_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_logs_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_logs_id_seq OWNED BY public.maintenance_logs.id;


--
-- Name: maintenance_operations; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_operations (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    maintenance_type_id bigint NOT NULL,
    maintenance_schedule_id bigint,
    provider_id bigint,
    status character varying(255) DEFAULT 'planned'::character varying NOT NULL,
    scheduled_date date,
    completed_date date,
    mileage_at_maintenance integer,
    duration_minutes integer,
    total_cost numeric(10,2),
    description text,
    notes text,
    created_by bigint NOT NULL,
    updated_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT maintenance_operations_status_check CHECK (((status)::text = ANY ((ARRAY['planned'::character varying, 'in_progress'::character varying, 'completed'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.maintenance_operations OWNER TO zenfleet_user;

--
-- Name: maintenance_operations_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_operations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_operations_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_operations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_operations_id_seq OWNED BY public.maintenance_operations.id;


--
-- Name: maintenance_plans; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_plans (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    maintenance_type_id bigint NOT NULL,
    recurrence_value integer NOT NULL,
    recurrence_unit_id bigint NOT NULL,
    next_due_date date,
    next_due_mileage bigint,
    notes text,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint
);


ALTER TABLE public.maintenance_plans OWNER TO zenfleet_user;

--
-- Name: maintenance_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_plans_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_plans_id_seq OWNED BY public.maintenance_plans.id;


--
-- Name: maintenance_providers; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_providers (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    company_name character varying(255),
    email character varying(255),
    phone character varying(50),
    address text,
    city character varying(100),
    postal_code character varying(20),
    specialties json,
    rating numeric(2,1),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.maintenance_providers OWNER TO zenfleet_user;

--
-- Name: maintenance_providers_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_providers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_providers_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_providers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_providers_id_seq OWNED BY public.maintenance_providers.id;


--
-- Name: maintenance_schedules; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_schedules (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    maintenance_type_id bigint NOT NULL,
    next_due_date date,
    next_due_mileage integer,
    interval_km integer,
    interval_days integer,
    alert_km_before integer DEFAULT 1000 NOT NULL,
    alert_days_before integer DEFAULT 7 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.maintenance_schedules OWNER TO zenfleet_user;

--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_schedules_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_schedules_id_seq OWNED BY public.maintenance_schedules.id;


--
-- Name: maintenance_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_statuses (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.maintenance_statuses OWNER TO zenfleet_user;

--
-- Name: maintenance_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_statuses_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_statuses_id_seq OWNED BY public.maintenance_statuses.id;


--
-- Name: maintenance_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_types (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    category character varying(255) NOT NULL,
    is_recurring boolean DEFAULT false NOT NULL,
    default_interval_km integer,
    default_interval_days integer,
    estimated_duration_minutes integer,
    estimated_cost numeric(10,2),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT maintenance_types_category_check CHECK (((category)::text = ANY ((ARRAY['preventive'::character varying, 'corrective'::character varying, 'inspection'::character varying, 'revision'::character varying])::text[])))
);


ALTER TABLE public.maintenance_types OWNER TO zenfleet_user;

--
-- Name: maintenance_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.maintenance_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.maintenance_types_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_types_id_seq OWNED BY public.maintenance_types.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO zenfleet_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO zenfleet_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    organization_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO zenfleet_user;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    organization_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO zenfleet_user;

--
-- Name: organization_metrics; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.organization_metrics (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    metric_date date NOT NULL,
    metric_period character varying(255) NOT NULL,
    total_users integer DEFAULT 0 NOT NULL,
    active_users integer DEFAULT 0 NOT NULL,
    total_vehicles integer DEFAULT 0 NOT NULL,
    active_vehicles integer DEFAULT 0 NOT NULL,
    total_distance_km numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    fuel_costs numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    maintenance_costs numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.organization_metrics OWNER TO zenfleet_user;

--
-- Name: organization_metrics_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.organization_metrics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organization_metrics_id_seq OWNER TO zenfleet_user;

--
-- Name: organization_metrics_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.organization_metrics_id_seq OWNED BY public.organization_metrics.id;


--
-- Name: organizations; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.organizations (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    name character varying(255) NOT NULL,
    legal_name character varying(255),
    organization_type character varying(255),
    industry character varying(255),
    description text,
    website character varying(255),
    phone_number character varying(255),
    logo_path character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    trade_register character varying(255),
    nif character varying(255),
    ai character varying(255),
    nis character varying(255),
    address character varying(255) NOT NULL,
    city character varying(255) NOT NULL,
    zip_code character varying(255),
    wilaya character varying(255) NOT NULL,
    scan_nif_path character varying(255),
    scan_ai_path character varying(255),
    scan_nis_path character varying(255),
    manager_first_name character varying(255),
    manager_last_name character varying(255),
    manager_nin character varying(255),
    manager_address character varying(255),
    manager_dob date,
    manager_pob character varying(255),
    manager_phone_number character varying(255),
    manager_id_scan_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    commune character varying(255),
    parent_organization_id bigint,
    organization_level character varying(255) DEFAULT 'company'::character varying NOT NULL,
    hierarchy_depth integer DEFAULT 0 NOT NULL,
    hierarchy_path character varying(255),
    is_tenant_root boolean DEFAULT true NOT NULL,
    allows_sub_organizations boolean DEFAULT false NOT NULL,
    tenant_settings json,
    data_retention_period integer DEFAULT 24 NOT NULL,
    audit_log_enabled boolean DEFAULT true NOT NULL,
    compliance_level character varying(255) DEFAULT 'standard'::character varying NOT NULL,
    compliance_certifications json,
    enabled_features json,
    feature_limits json,
    enforce_2fa boolean DEFAULT false NOT NULL,
    password_policy_level integer DEFAULT 1 NOT NULL,
    security_settings json,
    max_users integer,
    max_vehicles integer,
    monthly_cost numeric(10,2),
    slug character varying(255),
    brand_name character varying(255),
    registration_number character varying(255),
    tax_id character varying(255),
    primary_email character varying(255),
    billing_email character varying(255),
    support_email character varying(255),
    primary_phone character varying(255),
    mobile_phone character varying(255),
    headquarters_address json,
    billing_address json,
    compliance_status character varying(255) DEFAULT 'under_review'::character varying NOT NULL,
    status_changed_at timestamp(0) without time zone,
    status_reason text,
    subscription_plan character varying(255) DEFAULT 'trial'::character varying NOT NULL,
    subscription_tier character varying(255),
    subscription_starts_at timestamp(0) without time zone,
    subscription_expires_at timestamp(0) without time zone,
    trial_ends_at timestamp(0) without time zone,
    monthly_rate numeric(8,2),
    annual_rate numeric(8,2),
    currency character varying(3) DEFAULT 'EUR'::character varying NOT NULL,
    plan_limits json,
    current_usage json,
    feature_flags json,
    settings json,
    branding json,
    notification_preferences json,
    two_factor_required boolean DEFAULT false NOT NULL,
    ip_restriction_enabled boolean DEFAULT false NOT NULL,
    password_policy_strength integer DEFAULT 2 NOT NULL,
    session_timeout_minutes integer DEFAULT 480 NOT NULL,
    gdpr_compliant boolean DEFAULT false NOT NULL,
    gdpr_consent_at timestamp(0) without time zone,
    last_activity_at timestamp(0) without time zone,
    total_users integer DEFAULT 0 NOT NULL,
    active_users integer DEFAULT 0 NOT NULL,
    total_vehicles integer DEFAULT 0 NOT NULL,
    active_vehicles integer DEFAULT 0 NOT NULL,
    timezone character varying(255) DEFAULT 'Europe/Paris'::character varying NOT NULL,
    country_code character varying(2),
    language character varying(2) DEFAULT 'fr'::character varying NOT NULL,
    latitude numeric(10,8),
    longitude numeric(11,8),
    hierarchy_level integer DEFAULT 0 NOT NULL,
    created_by bigint,
    updated_by bigint,
    onboarding_completed_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    email character varying(255),
    CONSTRAINT organizations_compliance_status_check CHECK (((compliance_status)::text = ANY ((ARRAY['compliant'::character varying, 'warning'::character varying, 'non_compliant'::character varying, 'under_review'::character varying])::text[]))),
    CONSTRAINT organizations_subscription_plan_check CHECK (((subscription_plan)::text = ANY ((ARRAY['trial'::character varying, 'basic'::character varying, 'professional'::character varying, 'enterprise'::character varying, 'custom'::character varying])::text[])))
);


ALTER TABLE public.organizations OWNER TO zenfleet_user;

--
-- Name: organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organizations_id_seq OWNER TO zenfleet_user;

--
-- Name: organizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.organizations_id_seq OWNED BY public.organizations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO zenfleet_user;

--
-- Name: permission_scopes; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.permission_scopes (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    display_name character varying(255) NOT NULL,
    description text,
    scope_definition json NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_scopes OWNER TO zenfleet_user;

--
-- Name: permission_scopes_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.permission_scopes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permission_scopes_id_seq OWNER TO zenfleet_user;

--
-- Name: permission_scopes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.permission_scopes_id_seq OWNED BY public.permission_scopes.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint
);


ALTER TABLE public.permissions OWNER TO zenfleet_user;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO zenfleet_user;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO zenfleet_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO zenfleet_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: recurrence_units; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.recurrence_units (
    id bigint NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.recurrence_units OWNER TO zenfleet_user;

--
-- Name: recurrence_units_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.recurrence_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recurrence_units_id_seq OWNER TO zenfleet_user;

--
-- Name: recurrence_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.recurrence_units_id_seq OWNED BY public.recurrence_units.id;


--
-- Name: repair_categories; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.repair_categories (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(100) NOT NULL,
    description text,
    slug character varying(150) NOT NULL,
    icon character varying(50),
    color character varying(20) DEFAULT 'blue'::character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.repair_categories OWNER TO zenfleet_user;

--
-- Name: repair_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.repair_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.repair_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: repair_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.repair_categories_id_seq OWNED BY public.repair_categories.id;


--
-- Name: repair_notifications; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.repair_notifications (
    id bigint NOT NULL,
    repair_request_id bigint NOT NULL,
    user_id bigint NOT NULL,
    type character varying(50) NOT NULL,
    title character varying(255) NOT NULL,
    message text NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.repair_notifications OWNER TO zenfleet_user;

--
-- Name: repair_notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.repair_notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.repair_notifications_id_seq OWNER TO zenfleet_user;

--
-- Name: repair_notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.repair_notifications_id_seq OWNED BY public.repair_notifications.id;


--
-- Name: repair_request_history; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.repair_request_history (
    id bigint NOT NULL,
    repair_request_id bigint NOT NULL,
    user_id bigint,
    action character varying(50) NOT NULL,
    from_status character varying(50),
    to_status character varying(50) NOT NULL,
    comment text,
    metadata json,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.repair_request_history OWNER TO zenfleet_user;

--
-- Name: repair_request_history_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.repair_request_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.repair_request_history_id_seq OWNER TO zenfleet_user;

--
-- Name: repair_request_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.repair_request_history_id_seq OWNED BY public.repair_request_history.id;


--
-- Name: repair_requests; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.repair_requests (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    requested_by bigint NOT NULL,
    priority character varying(255) DEFAULT 'non_urgente'::character varying NOT NULL,
    status character varying(255) DEFAULT 'en_attente'::character varying NOT NULL,
    description text NOT NULL,
    location_description character varying(500),
    photos json,
    estimated_cost numeric(12,2),
    actual_cost numeric(12,2),
    supervisor_decision character varying(255),
    supervisor_id bigint,
    supervisor_comments text,
    supervisor_decided_at timestamp(0) without time zone,
    manager_decision character varying(255),
    manager_id bigint,
    manager_comments text,
    manager_decided_at timestamp(0) without time zone,
    assigned_supplier_id bigint,
    work_started_at timestamp(0) without time zone,
    work_completed_at timestamp(0) without time zone,
    requested_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    attachments json,
    work_photos json,
    completion_notes text,
    final_rating numeric(3,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    category_id bigint,
    uuid uuid NOT NULL,
    driver_id bigint,
    title character varying(255) NOT NULL,
    urgency character varying(20) DEFAULT 'normal'::character varying NOT NULL,
    current_mileage integer,
    current_location character varying(255),
    supervisor_status character varying(30),
    supervisor_comment text,
    supervisor_approved_at timestamp(0) without time zone,
    fleet_manager_id bigint,
    fleet_manager_status character varying(30),
    fleet_manager_comment text,
    fleet_manager_approved_at timestamp(0) without time zone,
    rejection_reason text,
    rejected_by bigint,
    rejected_at timestamp(0) without time zone,
    final_approved_by bigint,
    final_approved_at timestamp(0) without time zone,
    maintenance_operation_id bigint,
    CONSTRAINT repair_requests_manager_decision_check CHECK (((manager_decision)::text = ANY ((ARRAY['valide'::character varying, 'refuse'::character varying])::text[]))),
    CONSTRAINT repair_requests_priority_check CHECK (((priority)::text = ANY ((ARRAY['urgente'::character varying, 'a_prevoir'::character varying, 'non_urgente'::character varying])::text[]))),
    CONSTRAINT repair_requests_status_check CHECK (((status)::text = ANY ((ARRAY['en_attente'::character varying, 'accord_initial'::character varying, 'accordee'::character varying, 'refusee'::character varying, 'en_cours'::character varying, 'terminee'::character varying, 'annulee'::character varying])::text[]))),
    CONSTRAINT repair_requests_supervisor_decision_check CHECK (((supervisor_decision)::text = ANY ((ARRAY['accepte'::character varying, 'refuse'::character varying])::text[]))),
    CONSTRAINT valid_completion CHECK ((((status)::text <> 'terminee'::text) OR (((status)::text = 'terminee'::text) AND (work_completed_at IS NOT NULL) AND (actual_cost IS NOT NULL)))),
    CONSTRAINT valid_timing CHECK (((work_started_at IS NULL) OR (work_completed_at IS NULL) OR (work_started_at <= work_completed_at))),
    CONSTRAINT valid_workflow CHECK (((((status)::text = 'accord_initial'::text) AND ((supervisor_decision)::text = 'accepte'::text)) OR (((status)::text = 'accordee'::text) AND ((manager_decision)::text = 'valide'::text)) OR (((status)::text = 'refusee'::text) AND (((supervisor_decision)::text = 'refuse'::text) OR ((manager_decision)::text = 'refuse'::text))) OR ((status)::text = ANY ((ARRAY['en_attente'::character varying, 'en_cours'::character varying, 'terminee'::character varying, 'annulee'::character varying])::text[]))))
);


ALTER TABLE public.repair_requests OWNER TO zenfleet_user;

--
-- Name: repair_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.repair_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.repair_requests_id_seq OWNER TO zenfleet_user;

--
-- Name: repair_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.repair_requests_id_seq OWNED BY public.repair_requests.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO zenfleet_user;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    organization_id bigint,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO zenfleet_user;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO zenfleet_user;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: subscription_changes; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.subscription_changes (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    old_plan_id bigint,
    new_plan_id bigint NOT NULL,
    change_type character varying(255) NOT NULL,
    change_reason text,
    amount_due numeric(8,2),
    effective_date timestamp(0) without time zone NOT NULL,
    initiated_by bigint NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT subscription_changes_change_type_check CHECK (((change_type)::text = ANY ((ARRAY['upgrade'::character varying, 'downgrade'::character varying, 'renewal'::character varying, 'cancellation'::character varying])::text[]))),
    CONSTRAINT subscription_changes_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'processed'::character varying, 'failed'::character varying])::text[])))
);


ALTER TABLE public.subscription_changes OWNER TO zenfleet_user;

--
-- Name: subscription_changes_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.subscription_changes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.subscription_changes_id_seq OWNER TO zenfleet_user;

--
-- Name: subscription_changes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.subscription_changes_id_seq OWNED BY public.subscription_changes.id;


--
-- Name: subscription_plans; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.subscription_plans (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    tier character varying(255) NOT NULL,
    base_monthly_price numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    base_annual_price numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    feature_limits json NOT NULL,
    included_features json NOT NULL,
    trial_days integer DEFAULT 14 NOT NULL,
    is_public boolean DEFAULT true NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT subscription_plans_tier_check CHECK (((tier)::text = ANY ((ARRAY['trial'::character varying, 'basic'::character varying, 'professional'::character varying, 'enterprise'::character varying, 'custom'::character varying])::text[])))
);


ALTER TABLE public.subscription_plans OWNER TO zenfleet_user;

--
-- Name: subscription_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.subscription_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.subscription_plans_id_seq OWNER TO zenfleet_user;

--
-- Name: subscription_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.subscription_plans_id_seq OWNED BY public.subscription_plans.id;


--
-- Name: supervisor_driver_assignments; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.supervisor_driver_assignments (
    id bigint NOT NULL,
    supervisor_id bigint NOT NULL,
    driver_id bigint NOT NULL,
    assigned_by bigint NOT NULL,
    assigned_at timestamp(0) without time zone NOT NULL,
    expires_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.supervisor_driver_assignments OWNER TO zenfleet_user;

--
-- Name: supervisor_driver_assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.supervisor_driver_assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.supervisor_driver_assignments_id_seq OWNER TO zenfleet_user;

--
-- Name: supervisor_driver_assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.supervisor_driver_assignments_id_seq OWNED BY public.supervisor_driver_assignments.id;


--
-- Name: supplier_categories; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.supplier_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    organization_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.supplier_categories OWNER TO zenfleet_user;

--
-- Name: supplier_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.supplier_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.supplier_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: supplier_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.supplier_categories_id_seq OWNED BY public.supplier_categories.id;


--
-- Name: supplier_ratings; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.supplier_ratings (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    supplier_id bigint NOT NULL,
    repair_request_id bigint,
    rated_by bigint NOT NULL,
    quality_rating numeric(3,2) NOT NULL,
    timeliness_rating numeric(3,2) NOT NULL,
    communication_rating numeric(3,2) NOT NULL,
    pricing_rating numeric(3,2) NOT NULL,
    overall_rating numeric(3,2) NOT NULL,
    positive_feedback text,
    negative_feedback text,
    suggestions text,
    would_recommend boolean DEFAULT true NOT NULL,
    service_categories_rated json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT valid_ratings CHECK ((((quality_rating >= (1)::numeric) AND (quality_rating <= (10)::numeric)) AND ((timeliness_rating >= (1)::numeric) AND (timeliness_rating <= (10)::numeric)) AND ((communication_rating >= (1)::numeric) AND (communication_rating <= (10)::numeric)) AND ((pricing_rating >= (1)::numeric) AND (pricing_rating <= (10)::numeric)) AND ((overall_rating >= (1)::numeric) AND (overall_rating <= (10)::numeric))))
);


ALTER TABLE public.supplier_ratings OWNER TO zenfleet_user;

--
-- Name: supplier_ratings_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.supplier_ratings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.supplier_ratings_id_seq OWNER TO zenfleet_user;

--
-- Name: supplier_ratings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.supplier_ratings_id_seq OWNED BY public.supplier_ratings.id;


--
-- Name: suppliers; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.suppliers (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    supplier_type character varying(255) NOT NULL,
    company_name character varying(255) NOT NULL,
    trade_register character varying(50),
    nif character varying(20),
    nis character varying(20),
    ai character varying(20),
    contact_first_name character varying(100) NOT NULL,
    contact_last_name character varying(100) NOT NULL,
    contact_phone character varying(50) NOT NULL,
    contact_email character varying(255),
    address text NOT NULL,
    city character varying(100) NOT NULL,
    wilaya character varying(50) NOT NULL,
    commune character varying(100),
    postal_code character varying(10),
    phone character varying(50),
    email character varying(255),
    website character varying(500),
    specialties json DEFAULT '[]'::json NOT NULL,
    certifications json DEFAULT '[]'::json NOT NULL,
    service_areas json DEFAULT '[]'::json NOT NULL,
    rating numeric(3,2) DEFAULT 3.75,
    response_time_hours integer DEFAULT 24 NOT NULL,
    quality_score numeric(5,2) DEFAULT '75'::numeric,
    reliability_score numeric(5,2) DEFAULT '75'::numeric,
    contract_start_date date,
    contract_end_date date,
    payment_terms integer DEFAULT 30 NOT NULL,
    preferred_payment_method character varying(50) DEFAULT 'virement'::character varying NOT NULL,
    credit_limit numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    bank_name character varying(255),
    account_number character varying(50),
    rib character varying(20),
    is_active boolean DEFAULT true NOT NULL,
    is_preferred boolean DEFAULT false NOT NULL,
    is_certified boolean DEFAULT false NOT NULL,
    blacklisted boolean DEFAULT false NOT NULL,
    blacklist_reason text,
    documents json DEFAULT '[]'::json NOT NULL,
    notes text,
    total_orders integer DEFAULT 0 NOT NULL,
    total_amount_spent numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    last_order_date timestamp(0) without time zone,
    avg_order_value numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    completed_orders integer DEFAULT 0 NOT NULL,
    on_time_deliveries integer DEFAULT 0 NOT NULL,
    avg_response_time_hours numeric(5,2),
    customer_complaints integer DEFAULT 0 NOT NULL,
    last_evaluation_date timestamp(0) without time zone,
    auto_score_enabled boolean DEFAULT true NOT NULL,
    CONSTRAINT suppliers_supplier_type_check CHECK (((supplier_type)::text = ANY ((ARRAY['mecanicien'::character varying, 'assureur'::character varying, 'station_service'::character varying, 'pieces_detachees'::character varying, 'peinture_carrosserie'::character varying, 'pneumatiques'::character varying, 'electricite_auto'::character varying, 'controle_technique'::character varying, 'transport_vehicules'::character varying, 'autre'::character varying])::text[]))),
    CONSTRAINT valid_contract_dates CHECK (((contract_start_date IS NULL) OR (contract_end_date IS NULL) OR (contract_start_date <= contract_end_date))),
    CONSTRAINT valid_nif CHECK (((nif IS NULL) OR ((char_length((nif)::text) = 15) AND ((nif)::text ~ '^[0-9]{15}$'::text)))),
    CONSTRAINT valid_rating_range CHECK (((rating IS NULL) OR ((rating >= (0)::numeric) AND (rating <= (5)::numeric)))),
    CONSTRAINT valid_scores_range CHECK ((((quality_score IS NULL) OR ((quality_score >= (0)::numeric) AND (quality_score <= (100)::numeric))) AND ((reliability_score IS NULL) OR ((reliability_score >= (0)::numeric) AND (reliability_score <= (100)::numeric))))),
    CONSTRAINT valid_trade_register CHECK (((trade_register IS NULL) OR ((trade_register)::text ~ '^[0-9]{2}/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}$'::text)))
);


ALTER TABLE public.suppliers OWNER TO zenfleet_user;

--
-- Name: suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.suppliers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.suppliers_id_seq OWNER TO zenfleet_user;

--
-- Name: suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.suppliers_id_seq OWNED BY public.suppliers.id;


--
-- Name: transmission_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.transmission_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.transmission_types OWNER TO zenfleet_user;

--
-- Name: transmission_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.transmission_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transmission_types_id_seq OWNER TO zenfleet_user;

--
-- Name: transmission_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.transmission_types_id_seq OWNED BY public.transmission_types.id;


--
-- Name: user_organizations; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.user_organizations (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    organization_id bigint NOT NULL,
    role character varying(100) DEFAULT 'member'::character varying NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    specific_permissions json,
    scope_limitations json,
    granted_by_user_id bigint,
    granted_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    expires_at timestamp(0) without time zone,
    last_activity_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_organizations OWNER TO zenfleet_user;

--
-- Name: user_organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.user_organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_organizations_id_seq OWNER TO zenfleet_user;

--
-- Name: user_organizations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.user_organizations_id_seq OWNED BY public.user_organizations.id;


--
-- Name: user_validation_levels; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.user_validation_levels (
    user_id bigint NOT NULL,
    validation_level_id bigint NOT NULL
);


ALTER TABLE public.user_validation_levels OWNER TO zenfleet_user;

--
-- Name: user_vehicle; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.user_vehicle (
    user_id bigint NOT NULL,
    vehicle_id bigint NOT NULL
);


ALTER TABLE public.user_vehicle OWNER TO zenfleet_user;

--
-- Name: user_vehicle_assignments; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.user_vehicle_assignments (
    id bigint NOT NULL,
    supervisor_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    assigned_by bigint NOT NULL,
    assigned_at timestamp(0) without time zone NOT NULL,
    expires_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_vehicle_assignments OWNER TO zenfleet_user;

--
-- Name: user_vehicle_assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.user_vehicle_assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_vehicle_assignments_id_seq OWNER TO zenfleet_user;

--
-- Name: user_vehicle_assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.user_vehicle_assignments_id_seq OWNED BY public.user_vehicle_assignments.id;


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO zenfleet_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: validation_levels; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.validation_levels (
    id bigint NOT NULL,
    level_number smallint NOT NULL,
    name character varying(100) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.validation_levels OWNER TO zenfleet_user;

--
-- Name: validation_levels_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.validation_levels_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.validation_levels_id_seq OWNER TO zenfleet_user;

--
-- Name: validation_levels_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.validation_levels_id_seq OWNED BY public.validation_levels.id;


--
-- Name: vehicle_categories; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_categories (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(100) NOT NULL,
    code character varying(20) NOT NULL,
    description text,
    color_code character varying(20) DEFAULT '#3B82F6'::character varying NOT NULL,
    icon character varying(50),
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.vehicle_categories OWNER TO zenfleet_user;

--
-- Name: vehicle_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_categories_id_seq OWNED BY public.vehicle_categories.id;


--
-- Name: vehicle_depots; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_depots (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    code character varying(30),
    address text,
    city character varying(100),
    wilaya character varying(50),
    postal_code character varying(10),
    phone character varying(50),
    manager_name character varying(150),
    manager_phone character varying(50),
    capacity integer,
    current_count integer DEFAULT 0 NOT NULL,
    latitude numeric(10,8),
    longitude numeric(11,8),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    email character varying(255),
    description text
);


ALTER TABLE public.vehicle_depots OWNER TO zenfleet_user;

--
-- Name: vehicle_depots_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_depots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_depots_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_depots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_depots_id_seq OWNED BY public.vehicle_depots.id;


--
-- Name: vehicle_expenses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_expenses (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    supplier_id bigint,
    driver_id bigint,
    repair_request_id bigint,
    expense_category character varying(255) NOT NULL,
    expense_type character varying(100) NOT NULL,
    expense_subtype character varying(100),
    amount_ht numeric(15,2) NOT NULL,
    tva_rate numeric(5,2) DEFAULT '19'::numeric NOT NULL,
    tva_amount numeric(15,2) GENERATED ALWAYS AS (((amount_ht * tva_rate) / (100)::numeric)) STORED NOT NULL,
    total_ttc numeric(15,2) GENERATED ALWAYS AS ((amount_ht + ((amount_ht * tva_rate) / (100)::numeric))) STORED NOT NULL,
    invoice_number character varying(100),
    invoice_date date,
    receipt_number character varying(100),
    fiscal_receipt boolean DEFAULT false NOT NULL,
    odometer_reading integer,
    fuel_quantity numeric(10,3),
    fuel_price_per_liter numeric(8,3),
    fuel_type character varying(50),
    expense_latitude numeric(10,8),
    expense_longitude numeric(11,8),
    expense_city character varying(100),
    expense_wilaya character varying(50),
    needs_approval boolean DEFAULT false NOT NULL,
    approved boolean DEFAULT false NOT NULL,
    approved_by bigint,
    approved_at timestamp(0) without time zone,
    approval_comments text,
    payment_status character varying(50) DEFAULT 'pending'::character varying NOT NULL,
    payment_method character varying(50),
    payment_date date,
    payment_reference character varying(100),
    recorded_by bigint NOT NULL,
    expense_date date NOT NULL,
    description text NOT NULL,
    internal_notes text,
    tags json DEFAULT '[]'::json NOT NULL,
    custom_fields json DEFAULT '{}'::json NOT NULL,
    attachments json DEFAULT '[]'::json NOT NULL,
    is_recurring boolean DEFAULT false NOT NULL,
    recurrence_pattern character varying(50),
    next_due_date date,
    parent_expense_id bigint,
    requires_audit boolean DEFAULT false NOT NULL,
    audited boolean DEFAULT false NOT NULL,
    audited_by bigint,
    audited_at timestamp(0) without time zone,
    budget_allocated numeric(15,2),
    variance_percentage numeric(5,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    expense_group_id bigint,
    requester_id bigint,
    priority_level character varying(20) DEFAULT 'normal'::character varying NOT NULL,
    cost_center character varying(100),
    level1_approved boolean DEFAULT false NOT NULL,
    level1_approved_by bigint,
    level1_approved_at timestamp(0) without time zone,
    level1_comments text,
    level2_approved boolean DEFAULT false NOT NULL,
    level2_approved_by bigint,
    level2_approved_at timestamp(0) without time zone,
    level2_comments text,
    approval_status character varying(50) DEFAULT 'draft'::character varying NOT NULL,
    is_rejected boolean DEFAULT false NOT NULL,
    rejected_by bigint,
    rejected_at timestamp(0) without time zone,
    rejection_reason text,
    is_urgent boolean DEFAULT false NOT NULL,
    approval_deadline date,
    external_reference character varying(255),
    CONSTRAINT valid_amounts CHECK (((amount_ht >= (0)::numeric) AND (tva_rate >= (0)::numeric) AND (tva_rate <= (100)::numeric))),
    CONSTRAINT valid_approval_status_v2 CHECK (((approval_status)::text = ANY ((ARRAY['draft'::character varying, 'pending_level1'::character varying, 'pending_level2'::character varying, 'approved'::character varying, 'rejected'::character varying])::text[]))),
    CONSTRAINT valid_approval_workflow CHECK (((NOT needs_approval) OR (needs_approval AND approved AND (approved_by IS NOT NULL) AND (approved_at IS NOT NULL)) OR (needs_approval AND (NOT approved)))),
    CONSTRAINT valid_expense_date CHECK ((expense_date <= CURRENT_DATE)),
    CONSTRAINT valid_fuel_data CHECK ((((fuel_quantity IS NULL) AND (fuel_price_per_liter IS NULL)) OR ((fuel_quantity > (0)::numeric) AND (fuel_price_per_liter > (0)::numeric)))),
    CONSTRAINT valid_payment_data CHECK ((((payment_status)::text <> 'paid'::text) OR (((payment_status)::text = 'paid'::text) AND (payment_date IS NOT NULL)))),
    CONSTRAINT valid_priority_level CHECK (((priority_level)::text = ANY ((ARRAY['low'::character varying, 'normal'::character varying, 'high'::character varying, 'urgent'::character varying])::text[]))),
    CONSTRAINT valid_recurring_data CHECK (((NOT is_recurring) OR (is_recurring AND (recurrence_pattern IS NOT NULL)))),
    CONSTRAINT valid_two_level_approval CHECK ((((NOT level2_approved) OR level1_approved) AND ((NOT is_rejected) OR ((NOT level1_approved) AND (NOT level2_approved))))),
    CONSTRAINT vehicle_expenses_expense_category_check CHECK (((expense_category)::text = ANY ((ARRAY['maintenance_preventive'::character varying, 'reparation'::character varying, 'pieces_detachees'::character varying, 'carburant'::character varying, 'assurance'::character varying, 'controle_technique'::character varying, 'vignette'::character varying, 'amendes'::character varying, 'peage'::character varying, 'parking'::character varying, 'lavage'::character varying, 'transport'::character varying, 'formation_chauffeur'::character varying, 'autre'::character varying])::text[])))
);


ALTER TABLE public.vehicle_expenses OWNER TO zenfleet_user;

--
-- Name: COLUMN vehicle_expenses.expense_group_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.expense_group_id IS 'Lien vers le groupe de dépenses pour analyse par lot';


--
-- Name: COLUMN vehicle_expenses.requester_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.requester_id IS 'Utilisateur qui a initié la demande de dépense';


--
-- Name: COLUMN vehicle_expenses.priority_level; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.priority_level IS 'Priorité: low, normal, high, urgent';


--
-- Name: COLUMN vehicle_expenses.cost_center; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.cost_center IS 'Centre de coût pour comptabilité analytique';


--
-- Name: COLUMN vehicle_expenses.approval_status; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.approval_status IS 'draft, pending_level1, pending_level2, approved, rejected';


--
-- Name: COLUMN vehicle_expenses.external_reference; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_expenses.external_reference IS 'Référence système externe (ERP, comptabilité, etc.)';


--
-- Name: vehicle_expenses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_expenses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_expenses_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_expenses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_expenses_id_seq OWNED BY public.vehicle_expenses.id;


--
-- Name: vehicle_handover_details; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_handover_details (
    id bigint NOT NULL,
    handover_form_id bigint NOT NULL,
    category character varying(255) NOT NULL,
    item character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT vehicle_handover_details_status_check CHECK (((status)::text = ANY ((ARRAY['Bon'::character varying, 'Moyen'::character varying, 'Mauvais'::character varying, 'N/A'::character varying, 'Oui'::character varying, 'Non'::character varying])::text[])))
);


ALTER TABLE public.vehicle_handover_details OWNER TO zenfleet_user;

--
-- Name: vehicle_handover_details_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_handover_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_handover_details_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_handover_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_handover_details_id_seq OWNED BY public.vehicle_handover_details.id;


--
-- Name: vehicle_handover_forms; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_handover_forms (
    id bigint NOT NULL,
    assignment_id bigint NOT NULL,
    issue_date date NOT NULL,
    assignment_reason character varying(255),
    current_mileage bigint NOT NULL,
    general_observations text,
    additional_observations text,
    signed_form_path character varying(512),
    is_latest_version boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint
);


ALTER TABLE public.vehicle_handover_forms OWNER TO zenfleet_user;

--
-- Name: vehicle_handover_forms_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_handover_forms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_handover_forms_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_handover_forms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_handover_forms_id_seq OWNED BY public.vehicle_handover_forms.id;


--
-- Name: vehicle_mileage_readings; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_mileage_readings (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    recorded_by_id bigint,
    recorded_at timestamp(0) without time zone NOT NULL,
    mileage bigint NOT NULL,
    recording_method character varying(255) DEFAULT 'manual'::character varying NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT chk_mileage_positive CHECK ((mileage >= 0)),
    CONSTRAINT vehicle_mileage_readings_recording_method_check CHECK (((recording_method)::text = ANY ((ARRAY['manual'::character varying, 'automatic'::character varying])::text[])))
);


ALTER TABLE public.vehicle_mileage_readings OWNER TO zenfleet_user;

--
-- Name: TABLE vehicle_mileage_readings; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON TABLE public.vehicle_mileage_readings IS 'Relevés kilométriques des véhicules - Supporte relevés manuels et automatiques avec audit complet';


--
-- Name: COLUMN vehicle_mileage_readings.organization_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.organization_id IS 'Organisation propriétaire (multi-tenant isolation)';


--
-- Name: COLUMN vehicle_mileage_readings.vehicle_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.vehicle_id IS 'Véhicule concerné par le relevé';


--
-- Name: COLUMN vehicle_mileage_readings.recorded_by_id; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.recorded_by_id IS 'Utilisateur ayant enregistré le relevé (NULL si automatique)';


--
-- Name: COLUMN vehicle_mileage_readings.recorded_at; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.recorded_at IS 'Date et heure exacte du relevé';


--
-- Name: COLUMN vehicle_mileage_readings.mileage; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.mileage IS 'Valeur du kilométrage en kilomètres';


--
-- Name: COLUMN vehicle_mileage_readings.recording_method; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.recording_method IS 'Méthode: manual (saisie utilisateur) ou automatic (système)';


--
-- Name: COLUMN vehicle_mileage_readings.notes; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicle_mileage_readings.notes IS 'Observations ou commentaires sur le relevé';


--
-- Name: vehicle_mileage_readings_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_mileage_readings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_mileage_readings_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_mileage_readings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_mileage_readings_id_seq OWNED BY public.vehicle_mileage_readings.id;


--
-- Name: vehicle_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_statuses (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.vehicle_statuses OWNER TO zenfleet_user;

--
-- Name: vehicle_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_statuses_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_statuses_id_seq OWNED BY public.vehicle_statuses.id;


--
-- Name: vehicle_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.vehicle_types OWNER TO zenfleet_user;

--
-- Name: vehicle_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicle_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicle_types_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_types_id_seq OWNED BY public.vehicle_types.id;


--
-- Name: vehicles; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicles (
    id bigint NOT NULL,
    registration_plate character varying(50) NOT NULL,
    vin character varying(17),
    brand character varying(255),
    model character varying(255),
    color character varying(50),
    vehicle_type_id bigint,
    fuel_type_id bigint,
    transmission_type_id bigint,
    status_id bigint,
    manufacturing_year smallint,
    acquisition_date date,
    purchase_price numeric(12,2),
    current_value numeric(12,2),
    initial_mileage integer,
    current_mileage integer,
    engine_displacement_cc integer,
    power_hp integer,
    seats smallint,
    status_reason text,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    organization_id bigint,
    vehicle_name character varying(150),
    category_id bigint,
    depot_id bigint,
    is_archived boolean DEFAULT false NOT NULL
);


ALTER TABLE public.vehicles OWNER TO zenfleet_user;

--
-- Name: COLUMN vehicles.is_archived; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.vehicles.is_archived IS 'Indique si le véhicule est archivé';


--
-- Name: vehicles_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.vehicles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicles_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicles_id_seq OWNED BY public.vehicles.id;


--
-- Name: audit_logs_2025_04; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_04 FOR VALUES FROM ('2025-04-01 00:00:00+00') TO ('2025-05-01 00:00:00+00');


--
-- Name: audit_logs_2025_05; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_05 FOR VALUES FROM ('2025-05-01 00:00:00+00') TO ('2025-06-01 00:00:00+00');


--
-- Name: audit_logs_2025_06; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_06 FOR VALUES FROM ('2025-06-01 00:00:00+00') TO ('2025-07-01 00:00:00+00');


--
-- Name: audit_logs_2025_07; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_07 FOR VALUES FROM ('2025-07-01 00:00:00+00') TO ('2025-08-01 00:00:00+00');


--
-- Name: audit_logs_2025_08; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_08 FOR VALUES FROM ('2025-08-01 00:00:00+00') TO ('2025-09-01 00:00:00+00');


--
-- Name: audit_logs_2025_09; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_09 FOR VALUES FROM ('2025-09-01 00:00:00+00') TO ('2025-10-01 00:00:00+00');


--
-- Name: audit_logs_2025_10; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_10 FOR VALUES FROM ('2025-10-01 00:00:00+00') TO ('2025-11-01 00:00:00+00');


--
-- Name: audit_logs_2025_11; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_11 FOR VALUES FROM ('2025-11-01 00:00:00+00') TO ('2025-12-01 00:00:00+00');


--
-- Name: audit_logs_2025_12; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2025_12 FOR VALUES FROM ('2025-12-01 00:00:00+00') TO ('2026-01-01 00:00:00+00');


--
-- Name: audit_logs_2026_01; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2026_01 FOR VALUES FROM ('2026-01-01 00:00:00+00') TO ('2026-02-01 00:00:00+00');


--
-- Name: audit_logs_2026_02; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2026_02 FOR VALUES FROM ('2026-02-01 00:00:00+00') TO ('2026-03-01 00:00:00+00');


--
-- Name: audit_logs_2026_03; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2026_03 FOR VALUES FROM ('2026-03-01 00:00:00+00') TO ('2026-04-01 00:00:00+00');


--
-- Name: audit_logs_2026_04; Type: TABLE ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ATTACH PARTITION public.audit_logs_2026_04 FOR VALUES FROM ('2026-04-01 00:00:00+00') TO ('2026-05-01 00:00:00+00');


--
-- Name: algeria_communes id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_communes ALTER COLUMN id SET DEFAULT nextval('public.algeria_communes_id_seq'::regclass);


--
-- Name: assignments id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments ALTER COLUMN id SET DEFAULT nextval('public.assignments_id_seq'::regclass);


--
-- Name: comprehensive_audit_logs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass);


--
-- Name: contextual_permissions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions ALTER COLUMN id SET DEFAULT nextval('public.contextual_permissions_id_seq'::regclass);


--
-- Name: daily_metrics id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.daily_metrics ALTER COLUMN id SET DEFAULT nextval('public.daily_metrics_id_seq'::regclass);


--
-- Name: depot_assignment_history id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history ALTER COLUMN id SET DEFAULT nextval('public.depot_assignment_history_id_seq'::regclass);


--
-- Name: document_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories ALTER COLUMN id SET DEFAULT nextval('public.document_categories_id_seq'::regclass);


--
-- Name: document_revisions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_revisions ALTER COLUMN id SET DEFAULT nextval('public.document_revisions_id_seq'::regclass);


--
-- Name: documents id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents ALTER COLUMN id SET DEFAULT nextval('public.documents_id_seq'::regclass);


--
-- Name: driver_sanction_histories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanction_histories ALTER COLUMN id SET DEFAULT nextval('public.driver_sanction_histories_id_seq'::regclass);


--
-- Name: driver_sanctions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanctions ALTER COLUMN id SET DEFAULT nextval('public.driver_sanctions_id_seq'::regclass);


--
-- Name: driver_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses ALTER COLUMN id SET DEFAULT nextval('public.driver_statuses_id_seq'::regclass);


--
-- Name: drivers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers ALTER COLUMN id SET DEFAULT nextval('public.drivers_id_seq'::regclass);


--
-- Name: expense_audit_logs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.expense_audit_logs_id_seq'::regclass);


--
-- Name: expense_budgets id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_budgets ALTER COLUMN id SET DEFAULT nextval('public.expense_budgets_id_seq'::regclass);


--
-- Name: expense_groups id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups ALTER COLUMN id SET DEFAULT nextval('public.expense_groups_id_seq'::regclass);


--
-- Name: expense_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_types ALTER COLUMN id SET DEFAULT nextval('public.expense_types_id_seq'::regclass);


--
-- Name: expenses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses ALTER COLUMN id SET DEFAULT nextval('public.expenses_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: fuel_refills id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_refills ALTER COLUMN id SET DEFAULT nextval('public.fuel_refills_id_seq'::regclass);


--
-- Name: fuel_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_types ALTER COLUMN id SET DEFAULT nextval('public.fuel_types_id_seq'::regclass);


--
-- Name: granular_permissions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.granular_permissions ALTER COLUMN id SET DEFAULT nextval('public.granular_permissions_id_seq'::regclass);


--
-- Name: incident_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incident_statuses ALTER COLUMN id SET DEFAULT nextval('public.incident_statuses_id_seq'::regclass);


--
-- Name: incidents id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents ALTER COLUMN id SET DEFAULT nextval('public.incidents_id_seq'::regclass);


--
-- Name: maintenance_alerts id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts ALTER COLUMN id SET DEFAULT nextval('public.maintenance_alerts_id_seq'::regclass);


--
-- Name: maintenance_documents id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents ALTER COLUMN id SET DEFAULT nextval('public.maintenance_documents_id_seq'::regclass);


--
-- Name: maintenance_logs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs ALTER COLUMN id SET DEFAULT nextval('public.maintenance_logs_id_seq'::regclass);


--
-- Name: maintenance_operations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations ALTER COLUMN id SET DEFAULT nextval('public.maintenance_operations_id_seq'::regclass);


--
-- Name: maintenance_plans id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans ALTER COLUMN id SET DEFAULT nextval('public.maintenance_plans_id_seq'::regclass);


--
-- Name: maintenance_providers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_providers ALTER COLUMN id SET DEFAULT nextval('public.maintenance_providers_id_seq'::regclass);


--
-- Name: maintenance_schedules id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules ALTER COLUMN id SET DEFAULT nextval('public.maintenance_schedules_id_seq'::regclass);


--
-- Name: maintenance_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_statuses ALTER COLUMN id SET DEFAULT nextval('public.maintenance_statuses_id_seq'::regclass);


--
-- Name: maintenance_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types ALTER COLUMN id SET DEFAULT nextval('public.maintenance_types_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: organization_metrics id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organization_metrics ALTER COLUMN id SET DEFAULT nextval('public.organization_metrics_id_seq'::regclass);


--
-- Name: organizations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations ALTER COLUMN id SET DEFAULT nextval('public.organizations_id_seq'::regclass);


--
-- Name: permission_scopes id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permission_scopes ALTER COLUMN id SET DEFAULT nextval('public.permission_scopes_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: recurrence_units id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.recurrence_units ALTER COLUMN id SET DEFAULT nextval('public.recurrence_units_id_seq'::regclass);


--
-- Name: repair_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_categories ALTER COLUMN id SET DEFAULT nextval('public.repair_categories_id_seq'::regclass);


--
-- Name: repair_notifications id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_notifications ALTER COLUMN id SET DEFAULT nextval('public.repair_notifications_id_seq'::regclass);


--
-- Name: repair_request_history id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_request_history ALTER COLUMN id SET DEFAULT nextval('public.repair_request_history_id_seq'::regclass);


--
-- Name: repair_requests id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests ALTER COLUMN id SET DEFAULT nextval('public.repair_requests_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: subscription_changes id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes ALTER COLUMN id SET DEFAULT nextval('public.subscription_changes_id_seq'::regclass);


--
-- Name: subscription_plans id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_plans ALTER COLUMN id SET DEFAULT nextval('public.subscription_plans_id_seq'::regclass);


--
-- Name: supervisor_driver_assignments id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments ALTER COLUMN id SET DEFAULT nextval('public.supervisor_driver_assignments_id_seq'::regclass);


--
-- Name: supplier_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories ALTER COLUMN id SET DEFAULT nextval('public.supplier_categories_id_seq'::regclass);


--
-- Name: supplier_ratings id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings ALTER COLUMN id SET DEFAULT nextval('public.supplier_ratings_id_seq'::regclass);


--
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: transmission_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types ALTER COLUMN id SET DEFAULT nextval('public.transmission_types_id_seq'::regclass);


--
-- Name: user_organizations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations ALTER COLUMN id SET DEFAULT nextval('public.user_organizations_id_seq'::regclass);


--
-- Name: user_vehicle_assignments id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments ALTER COLUMN id SET DEFAULT nextval('public.user_vehicle_assignments_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: validation_levels id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.validation_levels ALTER COLUMN id SET DEFAULT nextval('public.validation_levels_id_seq'::regclass);


--
-- Name: vehicle_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_categories ALTER COLUMN id SET DEFAULT nextval('public.vehicle_categories_id_seq'::regclass);


--
-- Name: vehicle_depots id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_depots ALTER COLUMN id SET DEFAULT nextval('public.vehicle_depots_id_seq'::regclass);


--
-- Name: vehicle_expenses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses ALTER COLUMN id SET DEFAULT nextval('public.vehicle_expenses_id_seq'::regclass);


--
-- Name: vehicle_handover_details id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_details ALTER COLUMN id SET DEFAULT nextval('public.vehicle_handover_details_id_seq'::regclass);


--
-- Name: vehicle_handover_forms id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms ALTER COLUMN id SET DEFAULT nextval('public.vehicle_handover_forms_id_seq'::regclass);


--
-- Name: vehicle_mileage_readings id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_mileage_readings ALTER COLUMN id SET DEFAULT nextval('public.vehicle_mileage_readings_id_seq'::regclass);


--
-- Name: vehicle_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_statuses ALTER COLUMN id SET DEFAULT nextval('public.vehicle_statuses_id_seq'::regclass);


--
-- Name: vehicle_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_types ALTER COLUMN id SET DEFAULT nextval('public.vehicle_types_id_seq'::regclass);


--
-- Name: vehicles id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles ALTER COLUMN id SET DEFAULT nextval('public.vehicles_id_seq'::regclass);


--
-- Data for Name: algeria_communes; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.algeria_communes (id, wilaya_code, name_ar, name_fr, postal_code, is_active, created_at, updated_at) FROM stdin;
1	16	\N	Alger-Centre	16000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
2	16	\N	Bab El Oued	16020	t	2025-10-11 22:05:09	2025-10-11 22:05:09
3	31	\N	Oran	31000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
4	25	\N	Constantine	25000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
5	19	\N	Sétif	19000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
6	09	\N	Blida	09000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
7	05	\N	Batna	05000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
8	06	\N	Béjaïa	06000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
9	13	\N	Tlemcen	13000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
10	23	\N	Annaba	23000	t	2025-10-11 22:05:09	2025-10-11 22:05:09
\.


--
-- Data for Name: algeria_wilayas; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.algeria_wilayas (code, name_ar, name_fr, name_en, is_active, created_at, updated_at) FROM stdin;
01	\N	Adrar	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
02	\N	Chlef	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
03	\N	Laghouat	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
04	\N	Oum El Bouaghi	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
05	\N	Batna	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
06	\N	Béjaïa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
07	\N	Biskra	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
08	\N	Béchar	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
09	\N	Blida	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
10	\N	Bouira	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
11	\N	Tamanrasset	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
12	\N	Tébessa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
13	\N	Tlemcen	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
14	\N	Tiaret	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
15	\N	Tizi Ouzou	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
16	\N	Alger	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
17	\N	Djelfa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
18	\N	Jijel	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
19	\N	Sétif	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
20	\N	Saïda	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
21	\N	Skikda	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
22	\N	Sidi Bel Abbès	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
23	\N	Annaba	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
24	\N	Guelma	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
25	\N	Constantine	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
26	\N	Médéa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
27	\N	Mostaganem	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
28	\N	M'Sila	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
29	\N	Mascara	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
30	\N	Ouargla	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
31	\N	Oran	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
32	\N	El Bayadh	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
33	\N	Illizi	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
34	\N	Bordj Bou Arréridj	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
35	\N	Boumerdès	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
36	\N	El Tarf	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
37	\N	Tindouf	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
38	\N	Tissemsilt	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
39	\N	El Oued	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
40	\N	Khenchela	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
41	\N	Souk Ahras	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
42	\N	Tipaza	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
43	\N	Mila	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
44	\N	Aïn Defla	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
45	\N	Naâma	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
46	\N	Aïn Témouchent	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
47	\N	Ghardaïa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
48	\N	Relizane	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
49	\N	Timimoun	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
50	\N	Bordj Badji Mokhtar	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
51	\N	Ouled Djellal	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
52	\N	Béni Abbès	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
53	\N	In Salah	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
54	\N	In Guezzam	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
55	\N	Touggourt	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
56	\N	Djanet	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
57	\N	El M'Ghair	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
58	\N	El Meniaa	\N	t	2025-10-11 22:05:09	2025-10-11 22:05:09
\.


--
-- Data for Name: assignments; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.assignments (id, vehicle_id, driver_id, start_datetime, end_datetime, start_mileage, end_mileage, reason, notes, created_by_user_id, deleted_at, created_at, updated_at, organization_id, status, created_by, updated_by, ended_by_user_id, ended_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_04; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_04 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_05; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_05 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_06; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_06 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_07; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_07 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_08; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_08 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_09; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_09 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_10; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_10 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_11; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_11 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2025_12; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2025_12 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2026_01; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2026_01 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2026_02; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2026_02 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2026_03; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2026_03 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: audit_logs_2026_04; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.audit_logs_2026_04 (id, uuid, organization_id, user_id, event_category, event_type, event_action, resource_type, resource_id, resource_identifier, old_values, new_values, changes_summary, ip_address, user_agent, request_id, session_id, business_context, risk_level, compliance_tags, occurred_at, created_at) FROM stdin;
\.


--
-- Data for Name: contextual_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.contextual_permissions (id, user_id, organization_id, permission_scope_id, permission_name, context_filters, valid_from, valid_until, granted_by_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: daily_metrics; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.daily_metrics (id, metric_date, organization_id, total_vehicles, active_vehicles, total_drivers, active_drivers, daily_assignments, total_mileage, daily_maintenance_cost, daily_fuel_cost, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: depot_assignment_history; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.depot_assignment_history (id, vehicle_id, depot_id, organization_id, previous_depot_id, action, assigned_by, notes, assigned_at, created_at, updated_at) FROM stdin;
1	11	16	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 22:03:33	2025-11-05 22:03:33	2025-11-05 22:03:33
2	14	16	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 22:03:33	2025-11-05 22:03:33	2025-11-05 22:03:33
3	33	16	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 22:03:33	2025-11-05 22:03:33	2025-11-05 22:03:33
4	30	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
5	40	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
6	42	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
7	47	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
8	52	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
9	58	5	1	\N	assigned	4	BULK: Affectation par lot	2025-11-05 23:04:16	2025-11-05 23:04:16	2025-11-05 23:04:16
\.


--
-- Data for Name: document_categories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.document_categories (id, organization_id, name, description, is_active, created_at, updated_at, is_default, meta_schema, slug) FROM stdin;
\.


--
-- Data for Name: document_revisions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.document_revisions (id, document_id, user_id, file_path, original_filename, mime_type, size_in_bytes, extra_metadata, description, issue_date, expiry_date, revision_number, revision_notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: documentables; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.documentables (document_id, documentable_type, documentable_id) FROM stdin;
\.


--
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.documents (id, uuid, organization_id, document_category_id, user_id, file_path, original_filename, mime_type, size_in_bytes, issue_date, expiry_date, description, extra_metadata, created_at, updated_at, status, is_latest_version) FROM stdin;
\.


--
-- Data for Name: driver_sanction_histories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.driver_sanction_histories (id, created_at, updated_at, sanction_id, user_id, action, details, ip_address, user_agent) FROM stdin;
1	2025-10-14 01:31:43	2025-10-14 01:31:43	1	4	created	{"sanction_type":"avertissement_verbal","reason":"utilisation abusive de la moto jusqu'a des heures tardive durent la semaine ","sanction_date":"2025-08-12"}	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36
2	2025-10-14 01:33:30	2025-10-14 01:33:30	1	3	updated	{"changes":{"updated_at":"2025-10-14 01:33:30","archived_at":"2025-10-14 01:33:30"},"original":{"id":1,"created_at":"2025-10-14T00:31:43.000000Z","updated_at":"2025-10-14T00:31:43.000000Z","deleted_at":null,"organization_id":1,"driver_id":8,"supervisor_id":4,"sanction_type":"avertissement_verbal","reason":"utilisation abusive de la moto jusqu'a des heures tardive durent la semaine ","sanction_date":"2025-08-11T23:00:00.000000Z","attachment_path":null,"archived_at":null}}	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0
3	2025-10-14 01:33:30	2025-10-14 01:33:30	1	3	archived	{"archived_at":"2025-10-14 01:33:30"}	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0
4	2025-10-14 01:34:21	2025-10-14 01:34:21	1	3	updated	{"changes":{"updated_at":"2025-10-14 01:34:21","archived_at":null},"original":{"id":1,"created_at":"2025-10-14T00:31:43.000000Z","updated_at":"2025-10-14T00:33:30.000000Z","deleted_at":null,"organization_id":1,"driver_id":8,"supervisor_id":4,"sanction_type":"avertissement_verbal","reason":"utilisation abusive de la moto jusqu'a des heures tardive durent la semaine ","sanction_date":"2025-08-11T23:00:00.000000Z","attachment_path":null,"archived_at":"2025-10-14T00:33:30.000000Z"}}	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0
5	2025-10-14 01:34:21	2025-10-14 01:34:21	1	3	unarchived	{"unarchived_at":"2025-10-14 01:34:21"}	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0
\.


--
-- Data for Name: driver_sanctions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.driver_sanctions (id, created_at, updated_at, deleted_at, organization_id, driver_id, supervisor_id, sanction_type, reason, sanction_date, attachment_path, archived_at, severity, duration_days, status, notes) FROM stdin;
1	2025-10-14 01:31:43	2025-10-14 01:34:21	\N	1	8	4	avertissement_verbal	utilisation abusive de la moto jusqu'a des heures tardive durent la semaine 	2025-08-12	\N	\N	medium	\N	active	\N
\.


--
-- Data for Name: driver_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.driver_statuses (id, name, slug, description, color, text_color, icon, is_active, is_default, allows_assignments, is_available_for_work, sort_order, priority_level, organization_id, metadata, valid_from, valid_until, created_at, updated_at, deleted_at, can_drive, can_assign, requires_validation) FROM stdin;
1	Actif	active	Chauffeur actif et disponible pour les affectations	#10b981	white	fa-check-circle	t	f	t	t	1	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	t	t	f
2	En service	in-service	Chauffeur actuellement en mission	#3b82f6	white	fa-road	t	f	t	t	2	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	t	f	f
3	En congé	on-leave	Chauffeur en congé temporaire	#f59e0b	white	fa-calendar-times	t	f	t	t	3	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	f	f	f
4	En formation	in-training	Chauffeur en cours de formation	#8b5cf6	white	fa-graduation-cap	t	f	t	t	4	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	f	f	t
5	Suspendu	suspended	Chauffeur suspendu temporairement	#ef4444	white	fa-ban	f	f	t	t	5	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	f	f	t
6	Inactif	inactive	Chauffeur inactif ou non disponible	#6b7280	white	fa-user-slash	f	f	t	t	6	1	\N	\N	\N	\N	2025-10-11 22:55:32	2025-10-11 22:55:32	\N	f	f	f
\.


--
-- Data for Name: drivers; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.drivers (id, user_id, employee_number, first_name, last_name, photo, birth_date, blood_type, address, personal_phone, personal_email, license_number, license_issue_date, license_authority, recruitment_date, contract_end_date, status_id, emergency_contact_name, emergency_contact_phone, created_at, updated_at, deleted_at, full_address, license_expiry_date, organization_id, supervisor_id, license_categories, emergency_contact_relationship, notes) FROM stdin;
6	20	DLS-84745	zerrouk	ALIOUANE	drivers/photos/n2CrsympJ0sAh2wR8EParyAaWdSEgBx4cKa1kfPv.png	1986-04-19	O+	El Mouradia, BP 16100\r\nAlger	+213684849603	zaliouane@yahoo.fr	987-DZ-867	2025-07-20	Alger centre	2025-01-20	2027-01-23	1	Aliouane Cherif	0676135070	2025-10-12 23:59:59	2025-10-23 20:12:23	\N	\N	2035-07-20	1	\N	["B"]	\N	\N
8	22	DIF-2025-837	Said	merbouhi	drivers/photos/pVYpGOcweHjIEacsrchEl1BQL1LlidXYHavwtJ4N.png	1999-05-20	AB+	Cheraga, CP 16305,\r\nAlger	+213789056448	smerbouhi@gmail.com	DZ-0141503	2016-06-20	Daira El Biar	2022-12-03	2026-08-20	1	Merbouhi Ali	0674572386	2025-10-13 20:30:27	2025-10-23 20:16:03	\N	\N	2026-06-20	1	\N	["A","B"]	frère	aucun diplome
4	14	\N	TestRole	Verification	\N	1989-07-23	O+	quelque part au milieu de null part	+213778025640	test001@dontexist.dz	\N	\N	\N	\N	\N	1	\N	\N	2025-10-12 20:33:50	2025-10-22 16:56:10	2025-10-22 16:56:10	\N	\N	1	\N	\N	\N	\N
\.


--
-- Data for Name: expense_audit_logs; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.expense_audit_logs (id, organization_id, vehicle_expense_id, user_id, action, action_category, description, old_values, new_values, changed_fields, ip_address, user_agent, session_id, request_id, previous_status, new_status, previous_amount, new_amount, is_sensitive, requires_review, reviewed, reviewed_by, reviewed_at, review_notes, is_anomaly, anomaly_details, risk_level, metadata, tags, created_at) FROM stdin;
\.


--
-- Data for Name: expense_budgets; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.expense_budgets (id, organization_id, vehicle_id, expense_category, budget_period, budget_year, budget_month, budget_quarter, budgeted_amount, spent_amount, warning_threshold, critical_threshold, description, approval_workflow, is_active, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: expense_groups; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.expense_groups (id, organization_id, name, description, budget_allocated, budget_used, fiscal_year, fiscal_quarter, fiscal_month, is_active, alert_on_threshold, alert_threshold_percentage, block_on_exceeded, metadata, tags, responsible_users, created_by, updated_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: expense_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.expense_types (id, name, description) FROM stdin;
\.


--
-- Data for Name: expenses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.expenses (id, organization_id, vehicle_id, driver_id, expense_type_id, amount, expense_date, description, receipt_path, created_by_user_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: fuel_refills; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.fuel_refills (id, organization_id, vehicle_id, driver_id, refill_date, quantity_liters, price_per_liter, total_cost, mileage_at_refill, full_tank, station_name, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: fuel_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.fuel_types (id, name) FROM stdin;
1	Essence
2	Diesel
3	GPL
4	Électrique
\.


--
-- Data for Name: granular_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.granular_permissions (id, name, guard_name, module, resource, action, scope, description, risk_level, is_system, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: incident_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.incident_statuses (id, name) FROM stdin;
\.


--
-- Data for Name: incidents; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.incidents (id, organization_id, vehicle_id, driver_id, incident_date, type, severity, location, description, third_party_involved, police_report_number, insurance_claim_number, incident_status_id, estimated_cost, actual_cost, created_by_user_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: maintenance_alerts; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_alerts (id, organization_id, vehicle_id, maintenance_schedule_id, alert_type, priority, message, due_date, due_mileage, is_acknowledged, acknowledged_by, acknowledged_at, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: maintenance_documents; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_documents (id, organization_id, maintenance_operation_id, name, original_name, file_path, file_type, mime_type, file_size, document_type, description, metadata, uploaded_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: maintenance_logs; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_logs (id, vehicle_id, maintenance_plan_id, maintenance_type_id, maintenance_status_id, performed_on_date, performed_at_mileage, cost, details, performed_by, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: maintenance_operations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_operations (id, organization_id, vehicle_id, maintenance_type_id, maintenance_schedule_id, provider_id, status, scheduled_date, completed_date, mileage_at_maintenance, duration_minutes, total_cost, description, notes, created_by, updated_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: maintenance_plans; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_plans (id, vehicle_id, maintenance_type_id, recurrence_value, recurrence_unit_id, next_due_date, next_due_mileage, notes, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: maintenance_providers; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_providers (id, organization_id, name, company_name, email, phone, address, city, postal_code, specialties, rating, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: maintenance_schedules; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_schedules (id, organization_id, vehicle_id, maintenance_type_id, next_due_date, next_due_mileage, interval_km, interval_days, alert_km_before, alert_days_before, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: maintenance_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_statuses (id, name) FROM stdin;
\.


--
-- Data for Name: maintenance_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_types (id, organization_id, name, description, category, is_recurring, default_interval_km, default_interval_days, estimated_duration_minutes, estimated_cost, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2025_01_15_000000_create_organizations_table	1
6	2025_01_19_200000_create_algeria_tables_simple	1
7	2025_01_20_000000_add_gist_constraints_assignments	1
8	2025_01_20_100000_create_comprehensive_audit_logs	1
9	2025_01_20_101000_add_temporal_constraints_assignments	1
10	2025_01_20_102000_create_multi_tenant_system	1
11	2025_01_20_103000_create_work_orders_supply_chain	1
12	2025_01_20_105000_add_enterprise_optimizations_simple	1
13	2025_01_20_120000_create_assignments_enhanced_table	1
14	2025_01_21_100000_create_maintenance_types_table	1
15	2025_01_21_100100_create_maintenance_providers_table	1
17	2025_06_06_204451_create_vehicle_types_table	2
18	2025_06_06_204549_create_vehicle_statuses_table	3
19	2025_06_05_144141_create_permission_tables	4
20	2025_06_06_204514_create_fuel_types_table	5
21	2025_06_06_204531_create_transmission_types_table	5
22	2025_06_06_205007_create_vehicles_table	6
23	2025_06_07_231226_create_driver_statuses_table	7
24	2025_06_07_231452_create_drivers_table	7
25	2025_06_09_144736_create_assignments_table	8
26	2025_01_21_100200_create_maintenance_schedules_table	9
27	2025_01_21_100300_create_maintenance_operations_table	10
28	2025_01_21_100400_create_maintenance_alerts_table	10
29	2025_01_21_100500_create_maintenance_documents_table	10
30	2025_01_22_100000_create_repair_requests_table	10
31	2025_01_22_110000_create_suppliers_table	11
32	2025_01_22_111000_create_supplier_ratings_table	11
33	2025_01_22_120000_create_vehicle_expenses_table	12
34	2025_01_22_121000_create_expense_budgets_table	12
35	2025_01_26_120000_create_driver_statuses_table	13
36	2025_01_26_140000_add_missing_driver_columns	13
37	2025_01_26_141500_fix_drivers_table_constraints	14
38	2025_06_05_134749_add_custom_fields_to_users_table	14
39	2025_06_05_150327_create_validation_levels_table	14
40	2025_06_05_150533_create_user_validation_levels_table	14
41	2025_06_07_202347_add_soft_deletes_to_vehicles_table	14
42	2025_06_10_113936_add_license_expiry_date_to_drivers_table	14
43	2025_06_10_160701_create_maintenance_types_table	15
44	2025_06_10_160701_create_recurrence_units_table	15
45	2025_06_10_160702_create_maintenance_statuses_table	15
46	2025_06_10_160703_create_maintenance_plans_table	15
47	2025_06_10_160704_create_maintenance_logs_table	15
48	2025_06_17_233747_create_vehicle_handover_forms_table	15
49	2025_06_17_233757_create_vehicle_handover_details_table	15
50	2025_07_07_000238_add_organization_id_to_tables	15
51	2025_07_07_160317_add_soft_deletes_to_users_table	16
52	2025_07_22_012046_add_strategic_indexes_to_tables	16
53	2025_07_22_014038_create_fleet_management_extended_tables	16
54	2025_08_12_015505_create_user_vehicle_pivot_table	16
55	2025_08_15_014729_create_supplier_categories_table	16
56	2025_08_23_214824_create_document_categories_table	16
57	2025_08_23_214900_create_documents_table	16
58	2025_08_24_223300_add_is_default_and_meta_schema_to_document_categories_table	16
59	2025_09_06_101000_create_enhanced_rbac_system	16
60	2025_09_14_181221_add_slug_to_document_categories_table	16
61	2025_09_15_120000_update_organizations_table_structure	16
62	2025_09_25_123343_add_soft_deletes_to_users_table	16
63	2025_09_25_123433_create_essential_tables_for_assignments	16
64	2025_09_25_123703_add_soft_deletes_to_organizations_table	16
65	2025_09_25_123744_add_missing_fields_to_organizations	16
66	2025_09_26_000000_fix_organization_table_columns	16
67	2025_09_26_100000_create_enhanced_driver_statuses_table	17
68	2025_09_27_134545_make_vehicle_fields_nullable	18
69	2025_09_27_145000_add_deleted_at_to_maintenance_alerts_table	18
70	2025_10_01_225412_add_organization_id_to_permission_tables	19
71	2025_10_01_225906_update_roles_unique_constraint_for_teams	20
72	2025_10_03_140000_fix_vehicles_unique_constraints_multitenant	20
73	2025_10_05_000001_create_vehicle_categories_table	20
74	2025_10_05_000002_create_vehicle_depots_table	20
75	2025_10_05_000003_create_repair_requests_table	21
76	2025_10_05_000004_create_repair_request_history_table	21
77	2025_10_05_000005_create_repair_notifications_table	21
78	2025_10_05_000006_add_category_depot_name_to_vehicles	21
79	2025_10_05_000007_add_supervisor_to_drivers	21
80	2025_10_05_140000_create_vehicle_mileage_readings_table	21
81	2025_10_09_100000_create_repair_categories_table	21
82	2025_10_09_100001_add_category_id_to_repair_requests_table	21
83	2025_10_11_144250_add_missing_fields_to_assignments_table	22
84	2025_10_12_000001_align_repair_requests_schema	23
85	2025_10_12_150500_fix_driver_license_expiry_date_column	24
86	2025_10_13_000833_rename_photo_path_to_photo_in_drivers_table	25
87	2025_10_13_205834_create_driver_sanctions_table	26
88	2025_10_13_210059_create_driver_sanction_histories_table	27
89	2025_01_19_231500_add_enhanced_fields_to_driver_sanctions_table	28
90	2025_01_20_100000_add_is_archived_to_vehicles_table	29
91	2025_01_20_110000_update_license_categories_on_drivers_table	30
92	2025_01_20_120000_add_missing_fields_to_drivers_table	31
93	2025_10_23_100000_add_enterprise_features_to_documents_table	32
94	2025_10_23_100001_create_document_revisions_table	32
95	2025_10_23_100002_add_full_text_search_to_documents	32
96	2025_10_24_170000_update_trade_register_constraint	33
97	2025_10_24_230000_fix_suppliers_scores_precision	33
99	2025_10_27_000001_create_expense_groups_table	34
100	2025_10_27_000002_add_columns_to_vehicle_expenses	34
101	2025_10_27_000003_create_expense_audit_logs_table	35
102	2025_10_28_000001_add_expense_permissions	36
103	2025_10_28_020000_fix_suppliers_null_scores	37
104	2025_11_04_221700_create_depot_assignment_history_table	38
105	2025_11_05_120000_fix_vehicle_depots_code_nullable	39
106	2025_11_05_160000_add_missing_fields_to_vehicle_depots	40
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.model_has_permissions (permission_id, model_type, model_id, organization_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.model_has_roles (role_id, model_type, model_id, organization_id) FROM stdin;
3	App\\Models\\User	5	1
4	App\\Models\\User	6	1
6	App\\Models\\User	7	1
5	App\\Models\\User	14	1
5	App\\Models\\User	22	1
5	App\\Models\\User	20	1
1	App\\Models\\User	3	1
2	App\\Models\\User	4	1
\.


--
-- Data for Name: organization_metrics; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.organization_metrics (id, organization_id, metric_date, metric_period, total_users, active_users, total_vehicles, active_vehicles, total_distance_km, fuel_costs, maintenance_costs, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: organizations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.organizations (id, uuid, name, legal_name, organization_type, industry, description, website, phone_number, logo_path, status, trade_register, nif, ai, nis, address, city, zip_code, wilaya, scan_nif_path, scan_ai_path, scan_nis_path, manager_first_name, manager_last_name, manager_nin, manager_address, manager_dob, manager_pob, manager_phone_number, manager_id_scan_path, created_at, updated_at, commune, parent_organization_id, organization_level, hierarchy_depth, hierarchy_path, is_tenant_root, allows_sub_organizations, tenant_settings, data_retention_period, audit_log_enabled, compliance_level, compliance_certifications, enabled_features, feature_limits, enforce_2fa, password_policy_level, security_settings, max_users, max_vehicles, monthly_cost, slug, brand_name, registration_number, tax_id, primary_email, billing_email, support_email, primary_phone, mobile_phone, headquarters_address, billing_address, compliance_status, status_changed_at, status_reason, subscription_plan, subscription_tier, subscription_starts_at, subscription_expires_at, trial_ends_at, monthly_rate, annual_rate, currency, plan_limits, current_usage, feature_flags, settings, branding, notification_preferences, two_factor_required, ip_restriction_enabled, password_policy_strength, session_timeout_minutes, gdpr_compliant, gdpr_consent_at, last_activity_at, total_users, active_users, total_vehicles, active_vehicles, timezone, country_code, language, latitude, longitude, hierarchy_level, created_by, updated_by, onboarding_completed_at, deleted_at, email) FROM stdin;
1	a1b2c3d4-e5f6-7890-abcd-ef1234567890	Trans-Alger Logistics	Trans-Alger Logistics SARL	enterprise	Transport	Entreprise leader dans le transport et la logistique en Algérie, desservant toutes les wilayas du pays.	https://transalger.dz	+213 21 12 34 56	\N	active	16/20-123456 A 15	098765432109876	01234567890123	123456789012345	15 Rue Didouche Mourad	Alger-Centre	16000	16	\N	\N	\N	Ahmed	Benali	162012345678901234	45 Rue des Martyrs, Alger	1975-03-15	Alger	+213 21 98 76 54	\N	2023-01-15 08:30:00	2024-12-01 10:15:00	\N	\N	company	0	/1/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	trans-alger-logistics	\N	\N	\N	contact@transalger.dz	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
2	b2c3d4e5-f6a7-8901-bcde-f23456789012	Setif Fleet Services	Setif Fleet Services EURL	sme	Services	Gestion de flotte automobile pour PME dans la région des Hauts Plateaux.	\N	+213 36 45 67 89	\N	active	19/22-987654 B 28	198765432109876	98765432109876	987654321098765	25 Boulevard du 1er Novembre	Sétif	19000	19	\N	\N	\N	Fatima	Meziani	190585432167890123	12 Cité des Oliviers, Sétif	1985-07-22	Sétif	+213 36 78 90 12	\N	2023-05-20 14:20:00	2024-11-15 16:45:00	Sétif Centre	\N	company	0	/2/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	setif-fleet-services	\N	\N	\N	info@setiffleet.dz	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
3	c3d4e5f6-a7b8-9012-cdef-345678901234	Oran Maritime Transport	Oran Maritime Transport SPA	enterprise	Transport	Transport maritime et terrestre, import-export via le port d'Oran.	https://omtransport.dz	+213 41 23 45 67	\N	active	31/19-456789 A 42	318765432109876	31876543210987	318765432109876	8 Avenue de l'ALN	Oran	31000	31	\N	\N	\N	Karim	Benaissa	311278456789012345	102 Hai El Menzah, Oran	1978-12-10	Oran	+213 41 87 65 43	\N	2022-09-10 09:00:00	2024-10-30 11:30:00	Oran Centre	\N	company	0	/3/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	oran-maritime-transport	\N	\N	\N	direction@omtransport.dz	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
4	cb4ccda4-59e5-4c3c-ad87-a58231a57036	Stoltenberg LLC	Stoltenberg LLC EI	enterprise	Transport	Panther received knife and fork with a table set out under a tree a few minutes to see what would happen next. First, she tried to look over their slates; 'but it sounds uncommon nonsense.' Alice.	http://www.hintz.net/doloremque-officia-illo-nulla-dignissimos.html	+213 82 24 93 75 19	\N	active	41/11-325479 A 51	264218640395607	52769394013876	178242537329518	7987 Baumbach View Apt. 238	Souk Ahras	41000	41	\N	\N	\N	Annette	Lowe	703316588667376976	79228 Quigley Tunnel\nBaumbachburgh, VT 90254-9700	1971-07-22	North Rachael	+213 09 09 30 00 80	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	\N	\N	company	0	/4/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	stoltenberg-llc	\N	\N	\N	khalil.hilpert@damore.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
5	86ac0d2c-fe5b-4b7b-93be-a0fa598df1a8	Dickens, Rath and Murazik	Dickens, Rath and Murazik SARL	enterprise	Transport	Alice considered a little, half expecting to see if she did not at all this time, and was looking for the Dormouse,' thought Alice; 'I might as well look and see what would happen next. First, she.	http://bernier.info/vitae-rem-accusamus-veniam-eius-sed-in	+213 77 13 40 66 85	\N	active	13/15-825145 A 65	368933299064546	45552586174358	734071134870415	76534 Ritchie Lodge Suite 526	Tlemcen	13000	13	\N	\N	\N	Lane	Roberts	193458673146682743	476 Balistreri Place\nWesthaven, MO 49346	1982-04-01	West Carolanne	+213 24 23 63 05 82	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	Port Louisabury	\N	company	0	/5/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	dickens-rath-and-murazik	\N	\N	\N	lhill@purdy.org	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
6	1c9902ab-e035-437c-a4d6-5604e89588f8	Lueilwitz, Reinger and Bahringer	Lueilwitz, Reinger and Bahringer SARL	enterprise	Commerce	King said gravely, 'and go on in a fight with another dig of her or of anything to say, she simply bowed, and took the hookah into its face was quite surprised to see it trying in a low, trembling.	http://ziemann.org/quis-consectetur-id-vero-beatae	+213 92 92 34 16 20	\N	active	28/11-962629 A 29	761855377446526	88841137783126	970156715876985	8852 Mozell Parks Suite 795	M'Sila	28000	28	\N	\N	\N	Cecile	Parisian	535986112505782844	67394 Simonis Grove\nGenovevaport, LA 19568-1208	1978-03-15	Mitchellmouth	+213 35 88 59 06 76	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	\N	\N	company	0	/6/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	lueilwitz-reinger-and-bahringer	\N	\N	\N	finn38@weimann.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
7	50b616c7-525b-4897-bdef-2966850977f5	Cremin-McClure	Cremin-McClure SNC	sme	Logistique	Alice said; 'there's a large fan in the night? Let me think: was I the same thing as a drawing of a tree in the pool rippling to the law, And argued each case with MINE,' said the Hatter: 'let's all.	https://www.terry.org/architecto-eaque-recusandae-quam-excepturi-et-magnam-aut	+213 98 01 08 43 84	\N	active	34/10-970310 B 15	502155315621654	31282848455897	306051188412694	56788 Stehr Square	Bordj Bou Arréridj Centre	34000	34	\N	\N	\N	Gilda	Bahringer	597430051267985005	2634 Boehm Plaza Apt. 168\nSouth Delfinatown, DE 67934-5667	1962-09-06	New Dominic	+213 70 93 13 85 15	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	\N	\N	company	0	/7/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	cremin-mcclure	\N	\N	\N	jakubowski.nadia@ohara.net	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
8	0ab37b57-4c8d-4d68-ae31-99f24176f883	Beatty-McCullough	Beatty-McCullough EI	sme	Services	The long grass rustled at her for a rabbit! I suppose Dinah'll be sending me on messages next!' And she opened the door between us. For instance, if you want to stay with it as you say it.' 'That's.	http://www.lynch.org/expedita-nisi-cum-et-assumenda-impedit-reiciendis	+213 66 32 38 45 16	\N	active	12/12-826923 B 96	477324652439913	67374926580142	524926811338145	9150 Schuppe Union	Tébessa	12000	12	\N	\N	\N	Keith	Sipes	455524218224702601	5480 Kevin Fields\nJulieland, NM 34267	1962-07-20	Roweberg	+213 00 32 99 27 24	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	\N	\N	company	0	/8/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	beatty-mccullough	\N	\N	\N	clint71@runolfsson.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
9	5c3ad044-35ad-4a0b-a5b1-44c9797abe3c	Greenholt-Jaskolski	Greenholt-Jaskolski SNC	sme	Transport	Queen had only one way up as the Dormouse turned out, and, by the whole pack rose up into the air, and came back again. 'Keep your temper,' said the Hatter, 'when the Queen ordering off her.	http://gislason.info/cupiditate-dignissimos-sunt-quibusdam-in	+213 49 74 64 74 51	\N	active	47/11-769820 B 93	115743374689886	51269986614842	996579895334716	1341 Cole Cape Suite 497	Ghardaïa	47000	47	\N	\N	\N	Jeramy	O'Connell	330579704104562535	111 Patsy Tunnel Apt. 930\nWest Marvinstad, ID 25025-3607	2000-06-23	West Ahmadland	+213 30 50 17 02 04	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	South Lila	\N	company	0	/9/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	greenholt-jaskolski	\N	\N	\N	clare.thiel@wiza.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
10	dd61f727-a265-493c-9a97-cf780afdfb66	Kiehn, Turcotte and Mann	Kiehn, Turcotte and Mann EURL	sme	Commerce	I to do?' said Alice. 'I mean what I used to queer things happening. While she was about a thousand times as large as the game was going to shrink any further: she felt sure it would be offended.	https://ritchie.com/quia-excepturi-dolorem-dolorem-illum.html	+213 33 40 55 68 55	\N	active	17/14-128095 B 60	821395142894994	90049347890003	361658426849471	19775 Jeffery Place Apt. 133	Djelfa	17000	17	\N	\N	\N	Mark	Runolfsdottir	140117591005601780	545 Raquel Knolls Apt. 418\nSouth Kelsie, FL 85090	1972-11-11	Koelpinberg	+213 83 55 97 01 87	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	\N	\N	company	0	/10/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	kiehn-turcotte-and-mann	\N	\N	\N	gdickens@klein.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
11	8a35d331-8eba-44ca-8c25-2857ac33427f	Olson, Gleichner and Thompson	Olson, Gleichner and Thompson SPA	enterprise	Agriculture	Queen,' and she told her sister, who was sitting on a little bird as soon as there was Mystery,' the Mock Turtle in the other. In the very middle of her favourite word 'moral,' and the great wonder.	http://armstrong.org/	+213 73 49 42 40 19	\N	inactive	31/12-263510 A 45	999170634094030	64955164149253	039455090340122	832 Bernhard Hollow	Oran	31000	31	\N	\N	\N	Carleton	Klocko	591248293077750734	518 Baumbach Loop Apt. 339\nWunschview, GA 67119-4796	1969-01-22	Grahamberg	+213 33 97 99 57 24	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	Colinchester	\N	company	0	/11/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	olson-gleichner-and-thompson	\N	\N	\N	zfarrell@cremin.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
12	6a21207c-a7ba-4575-93c2-692b5dde266a	Rohan-Fay	Rohan-Fay SARL	ngo	Logistique	I don't put my arm round your waist,' the Duchess and the great puzzle!' And she opened the door that led into a large crowd collected round it: there were three gardeners instantly threw themselves.	http://www.effertz.com/nulla-pariatur-perspiciatis-excepturi-consequatur-magnam-vero-itaque	+213 98 95 58 57 88	\N	inactive	31/22-497692 B 58	040376403850041	90480398719739	112315245560404	13857 Von Village	Oran	31000	31	\N	\N	\N	Weston	Wilderman	763854689236319217	796 Langworth Unions Suite 561\nKoelpinmouth, WV 19162	1998-09-28	Feeneystad	+213 66 54 67 78 84	\N	2025-10-11 23:03:34	2025-10-11 23:03:34	New Aaliyahborough	\N	company	0	/12/	t	f	\N	24	t	standard	\N	\N	\N	f	1	\N	\N	\N	\N	rohan-fay	\N	\N	\N	esther.gleichner@hodkiewicz.com	\N	\N	\N	\N	\N	\N	under_review	\N	\N	trial	\N	\N	\N	\N	\N	\N	EUR	\N	\N	\N	\N	\N	\N	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	0	\N	\N	\N	\N	\N
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permission_scopes; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.permission_scopes (id, name, display_name, description, scope_definition, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.permissions (id, name, guard_name, created_at, updated_at, organization_id) FROM stdin;
99	view organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
100	create organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
101	edit organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
102	delete organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
103	restore organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
104	export organizations	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
105	manage organization settings	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
106	view organization statistics	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
107	view users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
108	create users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
109	edit users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
110	delete users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
111	restore users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
112	export users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
113	manage user roles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
114	reset user passwords	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
115	impersonate users	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
116	view roles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
117	manage roles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
118	view vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
119	create vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
120	edit vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
121	delete vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
122	restore vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
123	export vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
124	import vehicles	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
125	view vehicle history	web	2025-10-12 11:14:01	2025-10-12 11:14:01	\N
126	manage vehicle maintenance	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
127	manage vehicle documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
128	view drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
129	create drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
130	edit drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
131	delete drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
132	restore drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
133	export drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
134	import drivers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
135	view driver history	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
136	assign drivers to vehicles	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
137	manage driver licenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
138	view assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
139	create assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
140	edit assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
141	delete assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
142	end assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
143	extend assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
144	export assignments	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
145	view assignment calendar	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
146	view assignment gantt	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
147	view maintenance	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
148	manage maintenance plans	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
149	create maintenance operations	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
150	edit maintenance operations	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
151	delete maintenance operations	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
152	approve maintenance operations	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
153	export maintenance reports	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
154	view own repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
155	view team repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
156	view all repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
157	create repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
158	update own repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
159	delete repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
160	approve repair requests level 1	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
161	approve repair requests level 2	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
162	reject repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
163	export repair requests	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
164	view own mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
165	view team mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
166	view all mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
167	create mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
168	edit mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
169	delete mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
170	export mileage readings	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
171	view suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
172	create suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
173	edit suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
174	delete suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
175	restore suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
176	export suppliers	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
177	manage supplier contracts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
178	view expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
179	create expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
180	edit expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
181	delete expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
182	approve expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
183	export expenses	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
184	view expense analytics	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
185	view documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
186	create documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
187	edit documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
188	delete documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
189	download documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
190	approve documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
191	export documents	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
192	view analytics	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
193	view performance metrics	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
194	view roi metrics	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
195	export analytics	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
196	view alerts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
197	create alerts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
198	edit alerts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
199	delete alerts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
200	mark alerts as read	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
201	export alerts	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
202	view audit logs	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
203	export audit logs	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
204	view security audit	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
205	view user audit	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
206	view organization audit	web	2025-10-12 11:14:02	2025-10-12 11:14:02	\N
207	view own driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
208	view team driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
209	view all driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
210	create driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
211	update own driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
212	update any driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
213	delete driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
214	force delete driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
215	restore driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
216	archive driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
217	unarchive driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
218	export driver sanctions	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
219	view driver sanction statistics	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
220	view driver sanction history	web	2025-10-13 23:20:47	2025-10-13 23:20:47	\N
221	update vehicles	web	2025-10-22 23:01:19	2025-10-22 23:01:19	\N
224	force-delete vehicles	web	2025-10-22 23:26:47	2025-10-22 23:26:47	\N
225	assign vehicles	web	2025-10-22 23:26:47	2025-10-22 23:26:47	\N
226	view any expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
227	view expense	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
228	update expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
229	restore expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
230	force delete expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
231	approve expenses level1	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
232	approve expenses level2	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
233	reject expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
234	request expense approval	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
235	mark expenses as paid	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
236	cancel expense payment	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
237	manage expense payments	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
238	view expense reports	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
239	view expense dashboard	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
240	view expense statistics	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
241	view expense trends	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
242	view tco analysis	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
243	import expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
244	download expense reports	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
245	manage expense groups	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
246	manage expense budgets	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
247	manage expense categories	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
248	manage expense workflows	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
249	manage expense settings	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
250	view expense audit logs	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
251	export expense audit logs	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
252	bypass expense approval	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
253	edit approved expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
254	delete approved expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
255	view all organization expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
256	manage recurring expenses	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
257	set expense priorities	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
258	manage expense attachments	web	2025-10-29 00:10:17	2025-10-29 00:10:17	\N
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: recurrence_units; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.recurrence_units (id, name) FROM stdin;
\.


--
-- Data for Name: repair_categories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.repair_categories (id, organization_id, name, description, slug, icon, color, sort_order, is_active, metadata, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: repair_notifications; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.repair_notifications (id, repair_request_id, user_id, type, title, message, is_read, read_at, created_at) FROM stdin;
\.


--
-- Data for Name: repair_request_history; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.repair_request_history (id, repair_request_id, user_id, action, from_status, to_status, comment, metadata, created_at) FROM stdin;
\.


--
-- Data for Name: repair_requests; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.repair_requests (id, organization_id, vehicle_id, requested_by, priority, status, description, location_description, photos, estimated_cost, actual_cost, supervisor_decision, supervisor_id, supervisor_comments, supervisor_decided_at, manager_decision, manager_id, manager_comments, manager_decided_at, assigned_supplier_id, work_started_at, work_completed_at, requested_at, attachments, work_photos, completion_notes, final_rating, created_at, updated_at, deleted_at, category_id, uuid, driver_id, title, urgency, current_mileage, current_location, supervisor_status, supervisor_comment, supervisor_approved_at, fleet_manager_id, fleet_manager_status, fleet_manager_comment, fleet_manager_approved_at, rejection_reason, rejected_by, rejected_at, final_approved_by, final_approved_at, maintenance_operation_id) FROM stdin;
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
100	2
101	2
102	2
104	2
105	2
107	2
108	2
109	2
110	2
112	2
113	2
116	2
117	2
118	2
119	2
120	2
121	2
123	2
124	2
125	2
126	2
127	2
128	2
129	2
130	2
131	2
133	2
134	2
135	2
137	2
138	2
139	2
140	2
141	2
144	2
145	2
146	2
147	2
148	2
149	2
150	2
151	2
153	2
154	2
155	2
156	2
157	2
159	2
163	2
164	2
165	2
166	2
167	2
168	2
169	2
170	2
171	2
172	2
173	2
174	2
176	2
177	2
178	2
179	2
180	2
181	2
183	2
184	2
185	2
186	2
187	2
188	2
191	2
192	2
193	2
194	2
195	2
196	2
197	2
198	2
199	2
201	2
202	2
203	2
204	2
205	2
207	2
208	2
209	2
210	2
213	2
218	2
219	2
220	2
226	1
227	1
228	1
229	1
230	1
231	1
232	1
233	1
234	1
235	1
236	1
237	1
238	1
239	1
240	1
241	1
242	1
243	1
244	1
245	1
246	1
247	1
248	1
249	1
250	1
251	1
252	1
253	1
254	1
255	1
256	1
257	1
258	1
226	2
227	2
228	2
229	2
182	2
231	2
232	2
233	2
234	2
235	2
236	2
237	2
238	2
239	2
240	2
241	2
242	2
243	2
244	2
245	2
246	2
247	2
249	2
250	2
251	2
255	2
256	2
257	2
258	2
226	10
227	10
228	10
231	10
232	10
233	10
234	10
235	10
236	10
237	10
238	10
239	10
240	10
241	10
242	10
243	10
244	10
245	10
246	10
250	10
255	10
256	10
258	10
178	3
227	3
179	3
99	1
100	1
101	1
102	1
103	1
104	1
105	1
106	1
107	1
108	1
109	1
118	3
119	3
120	3
121	3
122	3
123	3
124	3
125	3
126	3
127	3
128	3
129	3
130	3
131	3
132	3
133	3
134	3
135	3
136	3
137	3
138	3
139	3
140	3
141	3
142	3
143	3
144	3
145	3
146	3
147	3
148	3
149	3
150	3
153	3
156	3
157	3
160	3
163	3
166	3
167	3
168	3
170	3
171	3
172	3
173	3
176	3
185	3
186	3
189	3
192	3
193	3
195	3
196	3
200	3
208	3
210	3
211	3
218	3
221	3
225	3
118	4
125	4
128	4
135	4
138	4
139	4
142	4
145	4
147	4
149	4
155	4
157	4
165	4
167	4
185	4
189	4
196	4
200	4
208	4
210	4
118	7
125	7
126	7
147	7
149	7
150	7
153	7
156	7
157	7
158	7
166	7
167	7
185	7
186	7
189	7
118	6
123	6
128	6
133	6
138	6
144	6
171	6
176	6
177	6
178	6
179	6
180	6
181	6
182	6
183	6
184	6
185	6
189	6
191	6
192	6
194	6
195	6
202	6
203	6
118	5
128	5
130	5
138	5
154	5
157	5
158	5
164	5
167	5
185	5
189	5
196	5
200	5
207	5
110	1
111	1
112	1
113	1
114	1
115	1
116	1
117	1
118	1
119	1
120	1
121	1
122	1
123	1
124	1
125	1
126	1
127	1
128	1
129	1
130	1
131	1
132	1
133	1
134	1
135	1
136	1
137	1
138	1
139	1
140	1
141	1
142	1
143	1
144	1
180	3
228	3
234	3
184	3
238	3
239	3
240	3
241	3
183	3
244	3
258	3
178	4
227	4
179	4
234	4
239	4
240	4
258	4
178	5
227	5
179	5
234	5
258	5
145	1
146	1
147	1
148	1
149	1
150	1
151	1
152	1
153	1
154	1
155	1
156	1
157	1
158	1
159	1
160	1
161	1
162	1
163	1
164	1
165	1
166	1
167	1
168	1
169	1
170	1
171	1
172	1
173	1
174	1
175	1
176	1
177	1
178	1
179	1
180	1
181	1
182	1
183	1
184	1
185	1
186	1
187	1
188	1
189	1
190	1
191	1
192	1
193	1
194	1
195	1
196	1
197	1
198	1
199	1
200	1
201	1
202	1
203	1
204	1
205	1
206	1
207	1
208	1
209	1
210	1
211	1
212	1
213	1
214	1
215	1
216	1
217	1
218	1
219	1
220	1
221	1
224	1
225	1
178	10
179	10
180	10
181	10
182	10
183	10
184	10
192	8
193	8
194	8
195	8
202	8
118	8
128	8
138	8
147	8
156	8
166	8
178	8
123	8
133	8
144	8
170	8
183	8
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.roles (id, organization_id, name, guard_name, created_at, updated_at) FROM stdin;
1	\N	Super Admin	web	2025-10-11 23:03:34	2025-10-11 23:03:34
2	\N	Admin	web	2025-10-11 23:03:34	2025-10-11 23:03:34
3	\N	Gestionnaire Flotte	web	2025-10-11 23:03:34	2025-10-11 23:03:34
4	\N	Superviseur	web	2025-10-11 23:03:34	2025-10-11 23:03:34
5	\N	Chauffeur	web	2025-10-11 23:03:34	2025-10-11 23:03:34
6	\N	Comptable	web	2025-10-11 23:03:34	2025-10-11 23:03:34
7	\N	Mécanicien	web	2025-10-11 23:03:34	2025-10-11 23:03:34
8	\N	Analyste	web	2025-10-11 23:03:34	2025-10-11 23:03:34
10	\N	Finance	web	2025-10-27 23:15:40	2025-10-27 23:15:40
\.


--
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.spatial_ref_sys (srid, auth_name, auth_srid, srtext, proj4text) FROM stdin;
\.


--
-- Data for Name: subscription_changes; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.subscription_changes (id, organization_id, old_plan_id, new_plan_id, change_type, change_reason, amount_due, effective_date, initiated_by, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: subscription_plans; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.subscription_plans (id, name, slug, description, tier, base_monthly_price, base_annual_price, feature_limits, included_features, trial_days, is_public, is_active, sort_order, created_at, updated_at) FROM stdin;
1	Trial	trial	Essai gratuit 14 jours	trial	0.00	0.00	{"max_users":3,"max_vehicles":10}	["basic_management"]	14	t	t	0	2025-10-11 22:54:19	2025-10-11 22:54:19
2	Professional	professional	Solution complète	professional	299.00	2990.00	{"max_users":100,"max_vehicles":500}	["advanced_management","analytics","api"]	14	t	t	0	2025-10-11 22:54:19	2025-10-11 22:54:19
3	Enterprise	enterprise	Solution enterprise	enterprise	999.00	9990.00	{"max_users":null,"max_vehicles":null}	["everything","white_labeling","sla"]	14	t	t	0	2025-10-11 22:54:19	2025-10-11 22:54:19
\.


--
-- Data for Name: supervisor_driver_assignments; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.supervisor_driver_assignments (id, supervisor_id, driver_id, assigned_by, assigned_at, expires_at, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: supplier_categories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.supplier_categories (id, name, organization_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: supplier_ratings; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.supplier_ratings (id, organization_id, supplier_id, repair_request_id, rated_by, quality_rating, timeliness_rating, communication_rating, pricing_rating, overall_rating, positive_feedback, negative_feedback, suggestions, would_recommend, service_categories_rated, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: suppliers; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.suppliers (id, organization_id, supplier_type, company_name, trade_register, nif, nis, ai, contact_first_name, contact_last_name, contact_phone, contact_email, address, city, wilaya, commune, postal_code, phone, email, website, specialties, certifications, service_areas, rating, response_time_hours, quality_score, reliability_score, contract_start_date, contract_end_date, payment_terms, preferred_payment_method, credit_limit, bank_name, account_number, rib, is_active, is_preferred, is_certified, blacklisted, blacklist_reason, documents, notes, total_orders, total_amount_spent, last_order_date, avg_order_value, created_at, updated_at, deleted_at, completed_orders, on_time_deliveries, avg_response_time_hours, customer_complaints, last_evaluation_date, auto_score_enabled) FROM stdin;
2	1	autre	dz lynx	16/00-12B6790243	867765073826498	NIS-9863637	\N	SELMANE	MOULOUD	0561614490	selmane@gmail.com	N° 47, lot communal D	El Achour	16	El Achour	16000	0770 90 99 00	selmane@dzlynx.com	https://dzlynx.com	[]	[]	[]	3.95	24	85.00	75.00	\N	\N	30	virement	0.00	\N	\N	\N	t	t	t	f	\N	[]	Fournisseur GPS	0	0.00	\N	0.00	2025-10-24 23:07:28	2025-10-24 23:08:29	2025-10-24 23:08:29	0	0	\N	0	2025-10-28 23:11:25	t
4	1	mecanicien	mecano Rouiba	\N	\N	\N	\N	SI rabah	EL BOUMBARDI	0770 56 53 92	\N	Quelque part à Rouiba	Rouiba	16	\N	\N	\N	\N	\N	[]	[]	[]	3.95	24	85.00	75.00	\N	\N	30	virement	0.00	\N	\N	\N	t	t	t	f	\N	[]	\N	0	0.00	\N	0.00	2025-10-28 01:23:08	2025-10-28 11:01:16	\N	0	0	\N	0	2025-10-28 23:11:25	t
\.


--
-- Data for Name: transmission_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.transmission_types (id, name) FROM stdin;
1	Manuelle
2	Automatique
\.


--
-- Data for Name: user_organizations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.user_organizations (id, user_id, organization_id, role, is_primary, is_active, specific_permissions, scope_limitations, granted_by_user_id, granted_at, expires_at, last_activity_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_validation_levels; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.user_validation_levels (user_id, validation_level_id) FROM stdin;
\.


--
-- Data for Name: user_vehicle; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.user_vehicle (user_id, vehicle_id) FROM stdin;
\.


--
-- Data for Name: user_vehicle_assignments; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.user_vehicle_assignments (id, supervisor_id, vehicle_id, assigned_by, assigned_at, expires_at, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, organization_id, deleted_at, first_name, last_name, phone, supervisor_id, manager_id, is_super_admin, permissions_cache, job_title, department, employee_id, hire_date, birth_date, two_factor_enabled, failed_login_attempts, locked_until, password_changed_at, last_activity_at, last_login_at, last_login_ip, login_count, is_active, user_status, timezone, language, preferences, notification_preferences, role, status) FROM stdin;
1		mohamed.meziani@trans-algerlogistics.local	2025-10-11 23:09:35	$2y$12$8GyM9uyn/mcV2GfOL9G2eul1W0btcVUfkYMrDcqSkxINf.jcG6laC	\N	2023-06-18 13:51:49	2025-10-11 23:09:35	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
2		amine.belabes@trans-algerlogistics.local	2025-10-11 23:10:33	$2y$12$tHEBrmnca5OI9WZGGF1OmeZhqn/GrJHCPzat3XD0hU2bSXmVgcG3m	\N	2021-12-22 02:20:47	2025-10-11 23:10:33	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
3	Super Administrateur	superadmin@zenfleet.dz	2025-10-12 01:05:17	$2y$12$0RY.mdnQjf0t.TGjo0pxXOQr8va1N7taYxLcxHFTvyi1XTeZhd89O	\N	2025-10-12 01:05:17	2025-10-12 01:05:17	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
5	Gestionnaire Flotte	gestionnaire@zenfleet.dz	2025-10-12 01:07:23	$2y$12$Z06lqpswDiGm1PEZVUTrPOdMf8xe6tMdnqs65W87pEpZiNXu.0Gtm	\N	2025-10-12 01:07:24	2025-10-12 01:07:24	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
6	Superviseur Transport	superviseur@zenfleet.dz	2025-10-12 01:07:24	$2y$12$hrRU3X2uvrST4e6kuPoBKuR4GNmVaVWJuBhP8RCdRhFquAg4TlY6G	\N	2025-10-12 01:07:24	2025-10-12 01:07:24	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
7	Comptable Finance	comptable@zenfleet.dz	2025-10-12 01:07:24	$2y$12$JgjoPkV28uN7RIKwIJGLXOQGM714qftMkWyvUCcTLXFy0fUFLHsre	\N	2025-10-12 01:07:24	2025-10-12 01:07:24	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
4	admin zenfleet	admin@zenfleet.dz	2025-10-12 01:07:23	$2y$12$snyKDE0o1xH6BnKyLS7rJe.ad4KKBd7JyJ6yzZB6Yr5CGhALlNjk.	\N	2025-10-12 01:07:23	2025-10-12 10:54:09	1	\N	admin	zenfleet	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
14		testroleverification@zenfleet.dz	\N	$2y$12$3IwZ/aA6nWbG3Ci9d7Yi7.UThDSEj5TYSj5rBblSyygsdHTnSNs6e	\N	2025-10-12 20:33:49	2025-10-12 20:33:49	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
22		saidmerbouhi@zenfleet.dz	\N	$2y$12$G950MikR2SQVMhEkiA6ROOwHDJQO7stOwro3zo0PRfGL0Mk0DVcHO	\N	2025-10-13 20:30:27	2025-10-13 20:30:27	1	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
20	zerrouk ALIOUANE	zerroukaliouane@zenfleet.dz	\N	$2y$12$P0BWw9O3hULi1GlifCkTf.YV.7Sy1NldhX8DSrQ.3746hscXx9.M6	\N	2025-10-12 23:59:59	2025-10-15 00:22:16	1	\N	zerrouk	ALIOUANE	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N	user	active
\.


--
-- Data for Name: validation_levels; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.validation_levels (id, level_number, name, description, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: vehicle_categories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_categories (id, organization_id, name, code, description, color_code, icon, sort_order, is_active, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: vehicle_depots; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_depots (id, organization_id, name, code, address, city, wilaya, postal_code, phone, manager_name, manager_phone, capacity, current_count, latitude, longitude, is_active, created_at, updated_at, deleted_at, email, description) FROM stdin;
16	1	Dépôt DG	DP0002	Zone industrielle reghaia	Reghaia	Alger		023658902	El hadi Chemli	0770 34 67 81	100	3	\N	\N	t	2025-11-05 20:30:03	2025-11-05 22:03:33	\N	depot-principal@zenfleet.com	Direction Générale
5	1	Dépôt Test Auto-Généré	DP0001	\N	Oran	Oran	\N	\N	\N	\N	30	6	\N	\N	t	2025-11-05 11:53:06	2025-11-05 23:04:16	\N	\N	\N
12	1	DEPOT_TEST_Minimal	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	\N	\N	t	2025-11-05 16:08:14	2025-11-06 00:12:17	2025-11-06 00:12:17	\N	\N
6	1	Dépôt Test Code NULL	\N	\N	Constantine	Constantine	\N	\N	\N	\N	25	0	\N	\N	f	2025-11-05 11:53:06	2025-11-05 11:53:06	\N	\N	\N
14	1	DEPOT_TEST_Modifié	CUSTOM-001	123 Boulevard de la République	Alger	Alger	16000	+213 555 0100	Ahmed Benali	+213 555 0101	150	0	36.75380000	3.05880000	f	2025-11-05 16:08:14	2025-11-05 20:30:52	\N	nouveau.email@zenfleet.com	Dépôt principal de la flotte ZenFleet à Alger
4	1	Dépôt Test Personnalisé	TEST-001	\N	Alger	Alger	\N	\N	\N	\N	50	0	\N	\N	t	2025-11-05 11:53:06	2025-11-05 20:31:11	\N	\N	\N
\.


--
-- Data for Name: vehicle_expenses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_expenses (id, organization_id, vehicle_id, supplier_id, driver_id, repair_request_id, expense_category, expense_type, expense_subtype, amount_ht, tva_rate, invoice_number, invoice_date, receipt_number, fiscal_receipt, odometer_reading, fuel_quantity, fuel_price_per_liter, fuel_type, expense_latitude, expense_longitude, expense_city, expense_wilaya, needs_approval, approved, approved_by, approved_at, approval_comments, payment_status, payment_method, payment_date, payment_reference, recorded_by, expense_date, description, internal_notes, tags, custom_fields, attachments, is_recurring, recurrence_pattern, next_due_date, parent_expense_id, requires_audit, audited, audited_by, audited_at, budget_allocated, variance_percentage, created_at, updated_at, deleted_at, expense_group_id, requester_id, priority_level, cost_center, level1_approved, level1_approved_by, level1_approved_at, level1_comments, level2_approved, level2_approved_by, level2_approved_at, level2_comments, approval_status, is_rejected, rejected_by, rejected_at, rejection_reason, is_urgent, approval_deadline, external_reference) FROM stdin;
\.


--
-- Data for Name: vehicle_handover_details; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_handover_details (id, handover_form_id, category, item, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: vehicle_handover_forms; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_handover_forms (id, assignment_id, issue_date, assignment_reason, current_mileage, general_observations, additional_observations, signed_form_path, is_latest_version, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: vehicle_mileage_readings; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_mileage_readings (id, organization_id, vehicle_id, recorded_by_id, recorded_at, mileage, recording_method, notes, created_at, updated_at) FROM stdin;
1	1	13	4	2025-10-22 14:12:00	120438	manual	A LA POMPE A ESSENCE	2025-10-23 01:13:33	2025-10-23 01:13:33
4	1	13	4	2025-10-24 17:00:00	122800	manual	fin de journéé	2025-10-26 11:30:22	2025-10-26 11:30:22
5	1	8	4	2025-10-13 12:03:00	71000	manual	\N	2025-11-03 01:34:06	2025-11-03 01:34:06
7	1	13	4	2025-10-22 14:20:00	123408	manual	chez un client	2025-11-05 23:02:51	2025-11-05 23:02:51
\.


--
-- Data for Name: vehicle_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_statuses (id, name) FROM stdin;
1	Actif
2	En maintenance
3	Inactif
\.


--
-- Data for Name: vehicle_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_types (id, name) FROM stdin;
1	Berline
2	Utilitaire
3	Camion
4	Bus
\.


--
-- Data for Name: vehicles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicles (id, registration_plate, vin, brand, model, color, vehicle_type_id, fuel_type_id, transmission_type_id, status_id, manufacturing_year, acquisition_date, purchase_price, current_value, initial_mileage, current_mileage, engine_displacement_cc, power_hp, seats, status_reason, notes, created_at, updated_at, deleted_at, organization_id, vehicle_name, category_id, depot_id, is_archived) FROM stdin;
2	534200-16	VF165979E2O782732	Toyota	Corolla	Noir	1	1	1	1	2018	2022-09-25	1227917.00	\N	5459	5459	\N	\N	7	\N	\N	2025-02-20 23:09:35	2025-10-05 23:09:35	\N	1	\N	\N	\N	f
3	465544-16	VF144393X4F887124	Ford	Escort	Blanc	1	1	1	1	2022	2024-11-08	1956387.00	\N	228196	228196	\N	\N	20	\N	\N	2025-04-18 23:09:35	2025-09-17 23:09:35	\N	1	\N	\N	\N	f
4	378745-16	VF187286N0M711000	Ford	Focus	Bleu	1	1	1	1	2023	2025-09-28	3530465.00	\N	172582	172582	\N	\N	2	\N	\N	2024-12-06 23:09:35	2025-09-11 23:09:35	\N	1	\N	\N	\N	f
5	403551-16	VF158905I3C713845	Toyota	Hilux	Bleu	1	1	1	1	2021	2023-09-11	3997249.00	\N	181532	181532	\N	\N	12	\N	\N	2025-03-07 23:09:35	2025-10-08 23:09:35	\N	1	\N	\N	\N	f
6	186125-16	VF164718Y9X738025	Toyota	Hilux	Vert	1	1	1	1	2012	2018-10-28	947993.00	\N	190248	190248	\N	\N	2	\N	\N	2024-10-28 23:09:35	2025-10-01 23:09:35	\N	1	\N	\N	\N	f
7	211523-16	VF162451Q2R340381	Renault	Sandero	Blanc	1	1	1	1	2014	2018-12-12	3793691.00	\N	73569	73569	\N	\N	12	\N	Ut magni autem asperiores.	2025-01-21 23:09:35	2025-09-27 23:09:35	\N	1	\N	\N	\N	f
9	754842-16	VF163617F3F091002	Hyundai	Accent	Bleu	1	1	1	1	2018	2024-04-23	3908571.00	\N	209718	209718	\N	\N	5	\N	\N	2025-07-03 23:09:35	2025-09-19 23:09:35	\N	1	\N	\N	\N	f
12	379418-16	VF189219Y6X746162	Iveco	Stralis	Rouge	1	1	1	1	2024	2025-10-11	3224905.00	\N	103559	103559	\N	\N	9	\N	\N	2025-02-16 23:09:35	2025-10-08 23:09:35	\N	1	\N	\N	\N	f
15	462192-16	VF122991J6G248843	Peugeot	Boxer	Vert	1	1	1	1	2017	2023-03-26	1389142.00	\N	54398	54398	\N	\N	2	\N	Accusantium dolorum alias odio iusto.	2025-04-12 23:09:35	2025-10-01 23:09:35	\N	1	\N	\N	\N	f
16	352064-16	VF116033N7Z163007	Isuzu	NQR	Rouge	1	1	1	1	2011	2024-09-01	4749012.00	\N	211013	211013	\N	\N	9	\N	\N	2024-10-31 23:09:35	2025-09-29 23:09:35	\N	1	\N	\N	\N	f
17	631035-16	VF195215C3L783152	Hyundai	i10	Noir	1	1	1	1	2013	2017-11-12	4848201.00	\N	214309	214309	\N	\N	45	\N	\N	2024-12-16 23:09:35	2025-09-19 23:09:35	\N	1	\N	\N	\N	f
18	576989-16	VF181818N6X268171	Isuzu	NQR	Noir	1	1	1	1	2017	2022-09-22	4551248.00	\N	9392	9392	\N	\N	12	\N	\N	2024-11-02 23:09:35	2025-09-11 23:09:35	\N	1	\N	\N	\N	f
19	884005-16	VF130462B9K759456	Toyota	Hiace	Vert	1	1	1	1	2019	2023-10-17	2905865.00	\N	214226	214226	\N	\N	45	\N	\N	2024-12-30 23:09:35	2025-09-23 23:09:35	\N	1	\N	\N	\N	f
20	513862-16	VF125375K3D385305	Peugeot	207	Rouge	1	1	1	1	2021	2023-07-26	2489881.00	\N	98171	98171	\N	\N	2	\N	Qui fugit excepturi quia ipsam aliquid quas modi qui.	2025-02-27 23:09:35	2025-10-04 23:09:35	\N	1	\N	\N	\N	f
21	730617-16	VF149301U4P194090	Peugeot	208	Blanc	1	1	1	1	2011	2014-10-13	2589803.00	\N	214911	214911	\N	\N	20	\N	\N	2025-02-13 23:09:35	2025-09-18 23:09:35	\N	1	\N	\N	\N	f
22	118910-16	VF131499W2O126556	Hyundai	Tucson	Gris	1	1	1	1	2012	2024-07-19	3158370.00	\N	209039	209039	\N	\N	9	\N	\N	2025-06-07 23:09:35	2025-09-22 23:09:35	\N	1	\N	\N	\N	f
23	917404-16	VF141390T4Y052349	Peugeot	308	Blanc	1	1	1	1	2011	2019-08-02	807130.00	\N	137416	137416	\N	\N	2	\N	Est tempora natus at quaerat sint.	2025-03-15 23:09:35	2025-10-09 23:09:35	\N	1	\N	\N	\N	f
24	590088-16	VF189855U4T515120	Toyota	Corolla	Vert	1	1	1	1	2024	2025-10-11	4923027.00	\N	140752	140752	\N	\N	2	\N	\N	2025-02-11 23:09:35	2025-10-06 23:09:35	\N	1	\N	\N	\N	f
25	770735-16	VF135550V3X044779	Toyota	Hiace	Gris	1	1	1	1	2022	2024-09-09	4068284.00	\N	280485	280485	\N	\N	9	\N	\N	2024-12-15 23:09:35	2025-10-05 23:09:35	\N	1	\N	\N	\N	f
26	105790-16	VF102667G2X843119	Peugeot	308	Vert	1	1	1	1	2020	2025-08-30	2100066.00	\N	294369	294369	\N	\N	45	\N	\N	2025-04-28 23:09:35	2025-09-30 23:09:35	\N	1	\N	\N	\N	f
27	216089-16	VF121956F6S817870	Peugeot	Partner	Rouge	1	1	1	1	2010	2019-03-01	4090915.00	\N	118414	118414	\N	\N	9	\N	\N	2025-04-19 23:09:35	2025-09-15 23:09:35	\N	1	\N	\N	\N	f
28	791763-16	VF170429X7E559490	Isuzu	NQR	Noir	1	1	1	1	2024	2025-10-11	1862353.00	\N	273624	273624	\N	\N	9	\N	\N	2024-10-24 23:09:35	2025-09-22 23:09:35	\N	1	\N	\N	\N	f
31	126902-16	VF165306Z0H447467	Volkswagen	Crafter	Vert	1	1	1	1	2023	2024-11-05	1939239.00	\N	236032	236032	\N	\N	20	\N	\N	2025-04-16 23:10:33	2025-09-13 23:10:33	\N	1	\N	\N	\N	f
34	194945-16	VF145357D5C322640	Volkswagen	Caddy	Gris	1	1	1	1	2011	2022-06-15	1602154.00	\N	169565	169565	\N	\N	7	\N	\N	2024-12-08 23:10:33	2025-10-05 23:10:33	\N	1	\N	\N	\N	f
36	210211-16	VF132844Y8R783202	Isuzu	FTR	Blanc	1	1	1	1	2010	2019-09-16	2830365.00	\N	166687	166687	\N	\N	7	\N	\N	2025-04-18 23:10:33	2025-10-04 23:10:33	\N	1	\N	\N	\N	f
37	927085-16	VF126543T6P357733	Volkswagen	Caddy	Noir	1	1	1	1	2024	2025-10-11	3284484.00	\N	37070	37070	\N	\N	9	\N	\N	2024-11-12 23:10:33	2025-09-21 23:10:33	\N	1	\N	\N	\N	f
38	139371-16	VF139527V0N786723	Hyundai	i10	Rouge	1	1	1	1	2019	2020-11-07	2359659.00	\N	133620	133620	\N	\N	20	\N	\N	2025-05-12 23:10:33	2025-09-28 23:10:33	\N	1	\N	\N	\N	f
39	229061-16	VF190008N8V498668	Isuzu	D-Max	Noir	1	1	1	1	2014	2020-01-10	2281958.00	\N	97397	97397	\N	\N	45	\N	\N	2024-11-23 23:10:33	2025-09-14 23:10:33	\N	1	\N	\N	\N	f
41	150814-16	VF182392P9M760516	Peugeot	Partner	Blanc	1	1	1	1	2017	2022-11-24	4414238.00	\N	68602	68602	\N	\N	7	\N	\N	2024-11-03 23:10:33	2025-10-07 23:10:33	\N	1	\N	\N	\N	f
43	523994-16	VF104636J1H427908	Toyota	Corolla	Gris	1	1	1	1	2011	2017-12-07	3108634.00	\N	258894	258894	\N	\N	7	\N	\N	2024-11-13 23:10:33	2025-09-29 23:10:33	\N	1	\N	\N	\N	f
35	755406-16	VF102904A6Y492551	Isuzu	NQR	Bleu	1	1	1	1	2015	2018-12-01	2308393.00	\N	107746	107746	\N	\N	20	\N	Voluptate corporis vitae vel et et non deleniti ut.	2025-08-16 23:10:33	2025-11-03 15:38:14	\N	1	\N	\N	\N	t
30	227013-16	VF125002V5E348893	Ford	Escort	Noir	1	1	1	1	2016	2019-08-31	2045761.00	\N	60114	60114	\N	\N	12	\N	\N	2025-07-24 23:10:33	2025-11-05 23:04:16	\N	1	\N	\N	5	f
32	444209-16	VF161893Z7I905567	Isuzu	FTR	Gris	1	1	1	1	2012	2024-02-20	2423649.00	\N	285115	285115	\N	\N	5	\N	\N	2025-09-30 23:10:33	2025-11-03 20:50:58	\N	1	\N	\N	\N	t
11	353018-16	VF156109B6U032018	Volkswagen	Golf	Gris	1	1	1	1	2021	2023-01-08	4890498.00	\N	246251	246251	\N	\N	45	\N	\N	2025-08-13 23:09:35	2025-11-05 22:03:33	\N	1	\N	\N	16	f
14	587449-16	VF162474A1U551965	Peugeot	206	Rouge	1	1	1	1	2022	2024-04-13	1529220.00	\N	185932	185932	\N	\N	45	\N	\N	2025-08-03 23:09:35	2025-11-05 22:03:33	\N	1	\N	\N	16	f
13	284139-16	VF102635T7E415988	Mercedes	A-Class	Rouge	1	1	1	1	2017	2023-11-23	2260240.00	\N	113438	123408	\N	\N	12	\N	\N	2025-04-04 23:09:35	2025-11-05 23:02:51	\N	1	\N	\N	\N	f
42	326385-16	VF134702S4O624607	Toyota	Hiace	Blanc	1	1	1	1	2024	2025-10-11	2969287.00	\N	52613	52613	\N	\N	20	\N	\N	2025-05-07 23:10:33	2025-11-05 23:04:16	\N	1	\N	\N	5	f
40	795626-16	VF184869Z5A458448	Ford	Fiesta	Blanc	1	1	1	1	2017	2021-05-04	2411585.00	\N	253860	253860	\N	\N	7	\N	\N	2025-05-12 23:10:33	2025-11-07 09:48:05	\N	1	\N	\N	5	t
10	589448-16	VF168274W9S785424	Renault	Clio	Blanc	1	1	1	2	2010	2013-09-05	1067400.00	\N	28211	28211	\N	\N	20	\N	\N	2025-05-28 23:09:35	2025-11-07 13:22:08	\N	1	\N	\N	\N	f
44	613014-16	VF172554F1J546405	Mercedes	Vito	Blanc	1	1	1	1	2017	2024-11-25	3003332.00	\N	213605	213605	\N	\N	12	\N	\N	2024-11-15 23:10:33	2025-10-02 23:10:33	\N	1	\N	\N	\N	f
45	130672-16	VF142419L6D253814	Renault	Logan	Bleu	1	1	1	1	2014	2021-03-02	2496769.00	\N	55431	55431	\N	\N	45	\N	\N	2025-03-21 23:10:33	2025-09-24 23:10:33	\N	1	\N	\N	\N	f
48	524416-16	VF182189E8M523399	Renault	Trafic	Vert	1	1	1	1	2021	2023-01-10	2795611.00	\N	135624	135624	\N	\N	20	\N	Voluptatem sequi veritatis sunt repellendus quidem sint et praesentium.	2025-04-09 23:10:33	2025-09-20 23:10:33	\N	1	\N	\N	\N	f
49	872437-16	VF157757M2E691347	Mercedes	Vito	Rouge	1	1	1	1	2017	2025-04-20	3622763.00	\N	159775	159775	\N	\N	12	\N	\N	2025-08-09 23:10:33	2025-09-13 23:10:33	\N	1	\N	\N	\N	f
50	844874-16	VF117046B1F613910	Peugeot	308	Rouge	1	1	1	1	2021	2023-09-28	4017032.00	\N	154108	154108	\N	\N	5	\N	\N	2025-02-26 23:10:33	2025-10-10 23:10:33	\N	1	\N	\N	\N	f
51	835292-16	VF178699S8F508211	Mercedes	Sprinter	Rouge	1	1	1	1	2021	2024-08-16	4466866.00	\N	274590	274590	\N	\N	12	\N	Dignissimos est enim aliquam quisquam.	2024-10-27 23:10:33	2025-09-27 23:10:33	\N	1	\N	\N	\N	f
54	377545-16	VF120478Y6E909205	Peugeot	301	Bleu	1	1	1	1	2010	2017-05-25	3404580.00	\N	128534	128534	\N	\N	20	\N	Consequatur corporis ex debitis est voluptatem voluptatum.	2025-04-02 23:10:33	2025-09-28 23:10:33	\N	1	\N	\N	\N	f
55	321971-16	VF103487F3Z744941	Isuzu	D-Max	Blanc	1	1	1	1	2024	2025-10-11	3167830.00	\N	231737	231737	\N	\N	20	\N	\N	2024-12-29 23:10:33	2025-10-04 23:10:33	\N	1	\N	\N	\N	f
46	611824-16	VF107903C8Y268423	Iveco	Stralis	Blanc	1	1	1	1	2024	2025-10-11	2389539.00	\N	166050	166050	\N	\N	45	\N	\N	2025-09-27 23:10:33	2025-10-22 23:25:00	\N	1	\N	\N	\N	t
56	869897-16	VF167582Y5S811254	Isuzu	D-Max	Bleu	1	1	1	1	2011	2017-12-15	1773162.00	\N	151614	151614	\N	\N	45	\N	\N	2025-08-14 23:10:33	2025-10-28 23:15:09	\N	1	\N	\N	\N	t
8	301401-16	VF141058T9T841124	Renault	Logan	Bleu	1	1	1	1	2010	2019-05-08	2451085.00	\N	70324	71000	\N	\N	20	\N	Nesciunt animi explicabo aperiam quis eum.	2025-02-03 23:09:35	2025-11-03 01:34:06	\N	1	\N	\N	\N	f
33	646447-16	VF186207J9I228504	Volkswagen	Jetta	Vert	1	1	1	1	2017	2021-07-21	3075175.00	\N	169401	169401	\N	\N	5	\N	Asperiores illo nisi ex nobis et.	2025-07-28 23:10:33	2025-11-05 22:03:33	\N	1	\N	\N	16	f
47	679937-16	VF164987K5V437083	Peugeot	301	Vert	1	1	1	1	2018	2020-07-07	3991326.00	\N	200600	200600	\N	\N	45	\N	Dolores rerum quas ut enim ullam eligendi sed.	2025-08-14 23:10:33	2025-11-05 23:04:16	\N	1	\N	\N	5	f
58	655642-16	VF195961M8T882880	Renault	Sandero	Bleu	1	1	1	2	2012	2019-01-20	3973061.00	\N	225759	226700	\N	\N	7	\N	\N	2025-07-20 23:10:33	2025-11-05 23:04:16	\N	1	\N	\N	5	f
52	891024-16	VF165654D4K799798	Peugeot	Partner	Blanc	1	1	1	1	2011	2024-12-01	4130295.00	\N	173448	173448	\N	\N	20	\N	Voluptatum consectetur rem tenetur eaque autem et.	2025-07-16 23:10:33	2025-11-07 09:48:05	\N	1	\N	\N	5	t
53	455989-16	VF121851I9F887433	Mercedes	Sprinter	Noir	1	1	1	2	2014	2025-06-21	3048425.00	\N	268221	268221	\N	\N	9	\N	\N	2025-06-08 23:10:33	2025-11-07 13:22:08	\N	1	\N	\N	\N	f
57	976929-16	VF189455P7X801722	Peugeot	Partner	Bleu	1	1	1	2	2016	2023-02-13	4694844.00	\N	7907	7907	\N	\N	12	\N	\N	2025-06-03 23:10:33	2025-11-07 13:22:08	\N	1	\N	\N	\N	f
\.


--
-- Data for Name: geocode_settings; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.geocode_settings (name, setting, unit, category, short_desc) FROM stdin;
\.


--
-- Data for Name: pagc_gaz; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_gaz (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_lex; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_lex (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_rules; Type: TABLE DATA; Schema: tiger; Owner: zenfleet_user
--

COPY tiger.pagc_rules (id, rule, is_custom) FROM stdin;
\.


--
-- Data for Name: topology; Type: TABLE DATA; Schema: topology; Owner: zenfleet_user
--

COPY topology.topology (id, name, srid, "precision", hasz) FROM stdin;
\.


--
-- Data for Name: layer; Type: TABLE DATA; Schema: topology; Owner: zenfleet_user
--

COPY topology.layer (topology_id, layer_id, schema_name, table_name, feature_column, feature_type, level, child_id) FROM stdin;
\.


--
-- Name: algeria_communes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.algeria_communes_id_seq', 10, true);


--
-- Name: assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.assignments_id_seq', 1, false);


--
-- Name: comprehensive_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.comprehensive_audit_logs_id_seq', 1, false);


--
-- Name: contextual_permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.contextual_permissions_id_seq', 1, false);


--
-- Name: daily_metrics_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.daily_metrics_id_seq', 1, false);


--
-- Name: depot_assignment_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.depot_assignment_history_id_seq', 9, true);


--
-- Name: document_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.document_categories_id_seq', 1, false);


--
-- Name: document_revisions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.document_revisions_id_seq', 1, false);


--
-- Name: documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.documents_id_seq', 1, false);


--
-- Name: driver_sanction_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.driver_sanction_histories_id_seq', 5, true);


--
-- Name: driver_sanctions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.driver_sanctions_id_seq', 1, true);


--
-- Name: driver_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.driver_statuses_id_seq', 6, true);


--
-- Name: drivers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.drivers_id_seq', 8, true);


--
-- Name: expense_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.expense_audit_logs_id_seq', 1, false);


--
-- Name: expense_budgets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.expense_budgets_id_seq', 1, false);


--
-- Name: expense_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.expense_groups_id_seq', 1, false);


--
-- Name: expense_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.expense_types_id_seq', 1, false);


--
-- Name: expenses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.expenses_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: fuel_refills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.fuel_refills_id_seq', 1, false);


--
-- Name: fuel_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.fuel_types_id_seq', 4, true);


--
-- Name: granular_permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.granular_permissions_id_seq', 1, false);


--
-- Name: incident_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.incident_statuses_id_seq', 1, false);


--
-- Name: incidents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.incidents_id_seq', 1, false);


--
-- Name: maintenance_alerts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_alerts_id_seq', 1, false);


--
-- Name: maintenance_documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_documents_id_seq', 1, false);


--
-- Name: maintenance_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_logs_id_seq', 1, false);


--
-- Name: maintenance_operations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_operations_id_seq', 1, false);


--
-- Name: maintenance_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_plans_id_seq', 1, false);


--
-- Name: maintenance_providers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_providers_id_seq', 1, false);


--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_schedules_id_seq', 1, false);


--
-- Name: maintenance_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_statuses_id_seq', 1, false);


--
-- Name: maintenance_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_types_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 106, true);


--
-- Name: organization_metrics_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.organization_metrics_id_seq', 1, false);


--
-- Name: organizations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.organizations_id_seq', 13, true);


--
-- Name: permission_scopes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.permission_scopes_id_seq', 1, false);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.permissions_id_seq', 258, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: recurrence_units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.recurrence_units_id_seq', 1, false);


--
-- Name: repair_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.repair_categories_id_seq', 1, false);


--
-- Name: repair_notifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.repair_notifications_id_seq', 1, false);


--
-- Name: repair_request_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.repair_request_history_id_seq', 1, false);


--
-- Name: repair_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.repair_requests_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 10, true);


--
-- Name: subscription_changes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.subscription_changes_id_seq', 1, false);


--
-- Name: subscription_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.subscription_plans_id_seq', 3, true);


--
-- Name: supervisor_driver_assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.supervisor_driver_assignments_id_seq', 1, false);


--
-- Name: supplier_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.supplier_categories_id_seq', 1, false);


--
-- Name: supplier_ratings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.supplier_ratings_id_seq', 1, false);


--
-- Name: suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.suppliers_id_seq', 4, true);


--
-- Name: transmission_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.transmission_types_id_seq', 2, true);


--
-- Name: user_organizations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.user_organizations_id_seq', 1, false);


--
-- Name: user_vehicle_assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.user_vehicle_assignments_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.users_id_seq', 22, true);


--
-- Name: validation_levels_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.validation_levels_id_seq', 1, false);


--
-- Name: vehicle_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_categories_id_seq', 1, false);


--
-- Name: vehicle_depots_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_depots_id_seq', 16, true);


--
-- Name: vehicle_expenses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_expenses_id_seq', 11, true);


--
-- Name: vehicle_handover_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_handover_details_id_seq', 1, false);


--
-- Name: vehicle_handover_forms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_handover_forms_id_seq', 1, false);


--
-- Name: vehicle_mileage_readings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_mileage_readings_id_seq', 7, true);


--
-- Name: vehicle_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_statuses_id_seq', 3, true);


--
-- Name: vehicle_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_types_id_seq', 4, true);


--
-- Name: vehicles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicles_id_seq', 60, true);


--
-- Name: topology_id_seq; Type: SEQUENCE SET; Schema: topology; Owner: zenfleet_user
--

SELECT pg_catalog.setval('topology.topology_id_seq', 1, false);


--
-- Name: algeria_communes algeria_communes_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_communes
    ADD CONSTRAINT algeria_communes_pkey PRIMARY KEY (id);


--
-- Name: algeria_wilayas algeria_wilayas_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_wilayas
    ADD CONSTRAINT algeria_wilayas_pkey PRIMARY KEY (code);


--
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


--
-- Name: comprehensive_audit_logs pk_audit_logs; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs
    ADD CONSTRAINT pk_audit_logs PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_04 audit_logs_2025_04_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_04
    ADD CONSTRAINT audit_logs_2025_04_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_05 audit_logs_2025_05_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_05
    ADD CONSTRAINT audit_logs_2025_05_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_06 audit_logs_2025_06_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_06
    ADD CONSTRAINT audit_logs_2025_06_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_07 audit_logs_2025_07_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_07
    ADD CONSTRAINT audit_logs_2025_07_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_08 audit_logs_2025_08_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_08
    ADD CONSTRAINT audit_logs_2025_08_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_09 audit_logs_2025_09_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_09
    ADD CONSTRAINT audit_logs_2025_09_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_10 audit_logs_2025_10_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_10
    ADD CONSTRAINT audit_logs_2025_10_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_11 audit_logs_2025_11_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_11
    ADD CONSTRAINT audit_logs_2025_11_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2025_12 audit_logs_2025_12_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2025_12
    ADD CONSTRAINT audit_logs_2025_12_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2026_01 audit_logs_2026_01_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2026_01
    ADD CONSTRAINT audit_logs_2026_01_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2026_02 audit_logs_2026_02_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2026_02
    ADD CONSTRAINT audit_logs_2026_02_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2026_03 audit_logs_2026_03_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2026_03
    ADD CONSTRAINT audit_logs_2026_03_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: audit_logs_2026_04 audit_logs_2026_04_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.audit_logs_2026_04
    ADD CONSTRAINT audit_logs_2026_04_pkey PRIMARY KEY (id, occurred_at);


--
-- Name: user_organizations chk_one_primary_per_user; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT chk_one_primary_per_user EXCLUDE USING btree (user_id WITH =) WHERE (((is_primary = true) AND (is_active = true)));


--
-- Name: contextual_permissions contextual_perm_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_perm_unique UNIQUE (user_id, organization_id, permission_scope_id, permission_name);


--
-- Name: contextual_permissions contextual_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_permissions_pkey PRIMARY KEY (id);


--
-- Name: daily_metrics daily_metrics_metric_date_organization_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.daily_metrics
    ADD CONSTRAINT daily_metrics_metric_date_organization_id_unique UNIQUE (metric_date, organization_id);


--
-- Name: daily_metrics daily_metrics_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.daily_metrics
    ADD CONSTRAINT daily_metrics_pkey PRIMARY KEY (id);


--
-- Name: depot_assignment_history depot_assignment_history_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_pkey PRIMARY KEY (id);


--
-- Name: document_categories document_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories
    ADD CONSTRAINT document_categories_pkey PRIMARY KEY (id);


--
-- Name: document_categories document_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories
    ADD CONSTRAINT document_categories_slug_unique UNIQUE (slug);


--
-- Name: document_revisions document_revisions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_revisions
    ADD CONSTRAINT document_revisions_pkey PRIMARY KEY (id);


--
-- Name: documentables documentables_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documentables
    ADD CONSTRAINT documentables_pkey PRIMARY KEY (document_id, documentable_id, documentable_type);


--
-- Name: documents documents_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_pkey PRIMARY KEY (id);


--
-- Name: documents documents_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_uuid_unique UNIQUE (uuid);


--
-- Name: driver_sanction_histories driver_sanction_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanction_histories
    ADD CONSTRAINT driver_sanction_histories_pkey PRIMARY KEY (id);


--
-- Name: driver_sanctions driver_sanctions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanctions
    ADD CONSTRAINT driver_sanctions_pkey PRIMARY KEY (id);


--
-- Name: driver_statuses driver_statuses_name_org_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_name_org_unique UNIQUE (name, organization_id);


--
-- Name: driver_statuses driver_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_pkey PRIMARY KEY (id);


--
-- Name: driver_statuses driver_statuses_slug_org_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_slug_org_unique UNIQUE (slug, organization_id);


--
-- Name: drivers drivers_employee_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_employee_number_unique UNIQUE (employee_number);


--
-- Name: drivers drivers_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_pkey PRIMARY KEY (id);


--
-- Name: drivers drivers_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_user_id_unique UNIQUE (user_id);


--
-- Name: expense_audit_logs expense_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs
    ADD CONSTRAINT expense_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: expense_budgets expense_budgets_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_budgets
    ADD CONSTRAINT expense_budgets_pkey PRIMARY KEY (id);


--
-- Name: expense_budgets expense_budgets_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_budgets
    ADD CONSTRAINT expense_budgets_unique UNIQUE (organization_id, vehicle_id, expense_category, budget_period, budget_year, budget_month, budget_quarter);


--
-- Name: expense_groups expense_groups_organization_id_name_fiscal_year_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups
    ADD CONSTRAINT expense_groups_organization_id_name_fiscal_year_unique UNIQUE (organization_id, name, fiscal_year);


--
-- Name: expense_groups expense_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups
    ADD CONSTRAINT expense_groups_pkey PRIMARY KEY (id);


--
-- Name: expense_types expense_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_types
    ADD CONSTRAINT expense_types_name_unique UNIQUE (name);


--
-- Name: expense_types expense_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_types
    ADD CONSTRAINT expense_types_pkey PRIMARY KEY (id);


--
-- Name: expenses expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fuel_refills fuel_refills_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_refills
    ADD CONSTRAINT fuel_refills_pkey PRIMARY KEY (id);


--
-- Name: fuel_types fuel_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_types
    ADD CONSTRAINT fuel_types_name_unique UNIQUE (name);


--
-- Name: fuel_types fuel_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_types
    ADD CONSTRAINT fuel_types_pkey PRIMARY KEY (id);


--
-- Name: granular_permissions granular_permissions_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.granular_permissions
    ADD CONSTRAINT granular_permissions_name_unique UNIQUE (name);


--
-- Name: granular_permissions granular_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.granular_permissions
    ADD CONSTRAINT granular_permissions_pkey PRIMARY KEY (id);


--
-- Name: incident_statuses incident_statuses_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incident_statuses
    ADD CONSTRAINT incident_statuses_name_unique UNIQUE (name);


--
-- Name: incident_statuses incident_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incident_statuses
    ADD CONSTRAINT incident_statuses_pkey PRIMARY KEY (id);


--
-- Name: incidents incidents_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_pkey PRIMARY KEY (id);


--
-- Name: maintenance_alerts maintenance_alerts_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts
    ADD CONSTRAINT maintenance_alerts_pkey PRIMARY KEY (id);


--
-- Name: maintenance_documents maintenance_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents
    ADD CONSTRAINT maintenance_documents_pkey PRIMARY KEY (id);


--
-- Name: maintenance_logs maintenance_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_pkey PRIMARY KEY (id);


--
-- Name: maintenance_operations maintenance_operations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT maintenance_operations_pkey PRIMARY KEY (id);


--
-- Name: maintenance_plans maintenance_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_pkey PRIMARY KEY (id);


--
-- Name: maintenance_providers maintenance_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_providers
    ADD CONSTRAINT maintenance_providers_pkey PRIMARY KEY (id);


--
-- Name: maintenance_schedules maintenance_schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT maintenance_schedules_pkey PRIMARY KEY (id);


--
-- Name: maintenance_statuses maintenance_statuses_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_statuses
    ADD CONSTRAINT maintenance_statuses_name_unique UNIQUE (name);


--
-- Name: maintenance_statuses maintenance_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_statuses
    ADD CONSTRAINT maintenance_statuses_pkey PRIMARY KEY (id);


--
-- Name: maintenance_types maintenance_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types
    ADD CONSTRAINT maintenance_types_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (organization_id, permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (organization_id, role_id, model_id, model_type);


--
-- Name: organization_metrics organization_metrics_organization_id_metric_date_metric_period_; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organization_metrics
    ADD CONSTRAINT organization_metrics_organization_id_metric_date_metric_period_ UNIQUE (organization_id, metric_date, metric_period);


--
-- Name: organization_metrics organization_metrics_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organization_metrics
    ADD CONSTRAINT organization_metrics_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_uuid_unique UNIQUE (uuid);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permission_scopes permission_scopes_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permission_scopes
    ADD CONSTRAINT permission_scopes_name_unique UNIQUE (name);


--
-- Name: permission_scopes permission_scopes_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permission_scopes
    ADD CONSTRAINT permission_scopes_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: recurrence_units recurrence_units_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.recurrence_units
    ADD CONSTRAINT recurrence_units_name_unique UNIQUE (name);


--
-- Name: recurrence_units recurrence_units_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.recurrence_units
    ADD CONSTRAINT recurrence_units_pkey PRIMARY KEY (id);


--
-- Name: repair_categories repair_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_categories
    ADD CONSTRAINT repair_categories_pkey PRIMARY KEY (id);


--
-- Name: repair_categories repair_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_categories
    ADD CONSTRAINT repair_categories_slug_unique UNIQUE (slug);


--
-- Name: repair_notifications repair_notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_notifications
    ADD CONSTRAINT repair_notifications_pkey PRIMARY KEY (id);


--
-- Name: repair_request_history repair_request_history_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_request_history
    ADD CONSTRAINT repair_request_history_pkey PRIMARY KEY (id);


--
-- Name: repair_requests repair_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_pkey PRIMARY KEY (id);


--
-- Name: repair_requests repair_requests_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_uuid_unique UNIQUE (uuid);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_organization_id_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_organization_id_name_guard_name_unique UNIQUE (organization_id, name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: subscription_changes subscription_changes_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes
    ADD CONSTRAINT subscription_changes_pkey PRIMARY KEY (id);


--
-- Name: subscription_plans subscription_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_plans
    ADD CONSTRAINT subscription_plans_pkey PRIMARY KEY (id);


--
-- Name: subscription_plans subscription_plans_slug_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_plans
    ADD CONSTRAINT subscription_plans_slug_unique UNIQUE (slug);


--
-- Name: supervisor_driver_assignments supervisor_driver_assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments
    ADD CONSTRAINT supervisor_driver_assignments_pkey PRIMARY KEY (id);


--
-- Name: supervisor_driver_assignments supervisor_driver_assignments_supervisor_id_driver_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments
    ADD CONSTRAINT supervisor_driver_assignments_supervisor_id_driver_id_unique UNIQUE (supervisor_id, driver_id);


--
-- Name: supplier_categories supplier_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories
    ADD CONSTRAINT supplier_categories_pkey PRIMARY KEY (id);


--
-- Name: supplier_ratings supplier_ratings_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_pkey PRIMARY KEY (id);


--
-- Name: supplier_ratings supplier_ratings_repair_request_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_repair_request_id_unique UNIQUE (repair_request_id);


--
-- Name: suppliers suppliers_nif_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_nif_unique UNIQUE (nif);


--
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


--
-- Name: suppliers suppliers_trade_register_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_trade_register_unique UNIQUE (trade_register);


--
-- Name: transmission_types transmission_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types
    ADD CONSTRAINT transmission_types_name_unique UNIQUE (name);


--
-- Name: transmission_types transmission_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types
    ADD CONSTRAINT transmission_types_pkey PRIMARY KEY (id);


--
-- Name: maintenance_schedules unq_maintenance_schedules_vehicle_type; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT unq_maintenance_schedules_vehicle_type UNIQUE (vehicle_id, maintenance_type_id);


--
-- Name: maintenance_types unq_maintenance_types_org_name; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types
    ADD CONSTRAINT unq_maintenance_types_org_name UNIQUE (organization_id, name);


--
-- Name: vehicle_categories unq_vehicle_categories_org_code; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_categories
    ADD CONSTRAINT unq_vehicle_categories_org_code UNIQUE (organization_id, code);


--
-- Name: vehicle_categories unq_vehicle_categories_org_name; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_categories
    ADD CONSTRAINT unq_vehicle_categories_org_name UNIQUE (organization_id, name);


--
-- Name: vehicle_depots unq_vehicle_depots_org_code; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_depots
    ADD CONSTRAINT unq_vehicle_depots_org_code UNIQUE (organization_id, code);


--
-- Name: vehicle_depots unq_vehicle_depots_org_name; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_depots
    ADD CONSTRAINT unq_vehicle_depots_org_name UNIQUE (organization_id, name);


--
-- Name: user_organizations user_org_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT user_org_unique UNIQUE (user_id, organization_id);


--
-- Name: user_organizations user_organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT user_organizations_pkey PRIMARY KEY (id);


--
-- Name: user_validation_levels user_validation_levels_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_validation_levels
    ADD CONSTRAINT user_validation_levels_pkey PRIMARY KEY (user_id, validation_level_id);


--
-- Name: user_vehicle_assignments user_vehicle_assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments
    ADD CONSTRAINT user_vehicle_assignments_pkey PRIMARY KEY (id);


--
-- Name: user_vehicle_assignments user_vehicle_assignments_supervisor_id_vehicle_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments
    ADD CONSTRAINT user_vehicle_assignments_supervisor_id_vehicle_id_unique UNIQUE (supervisor_id, vehicle_id);


--
-- Name: user_vehicle user_vehicle_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle
    ADD CONSTRAINT user_vehicle_pkey PRIMARY KEY (user_id, vehicle_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_phone_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_phone_unique UNIQUE (phone);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: validation_levels validation_levels_level_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.validation_levels
    ADD CONSTRAINT validation_levels_level_number_unique UNIQUE (level_number);


--
-- Name: validation_levels validation_levels_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.validation_levels
    ADD CONSTRAINT validation_levels_pkey PRIMARY KEY (id);


--
-- Name: vehicle_categories vehicle_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_categories
    ADD CONSTRAINT vehicle_categories_pkey PRIMARY KEY (id);


--
-- Name: vehicle_depots vehicle_depots_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_depots
    ADD CONSTRAINT vehicle_depots_pkey PRIMARY KEY (id);


--
-- Name: vehicle_expenses vehicle_expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_pkey PRIMARY KEY (id);


--
-- Name: vehicle_handover_details vehicle_handover_details_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_details
    ADD CONSTRAINT vehicle_handover_details_pkey PRIMARY KEY (id);


--
-- Name: vehicle_handover_forms vehicle_handover_forms_assignment_id_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms
    ADD CONSTRAINT vehicle_handover_forms_assignment_id_unique UNIQUE (assignment_id);


--
-- Name: vehicle_handover_forms vehicle_handover_forms_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms
    ADD CONSTRAINT vehicle_handover_forms_pkey PRIMARY KEY (id);


--
-- Name: vehicle_mileage_readings vehicle_mileage_readings_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_mileage_readings
    ADD CONSTRAINT vehicle_mileage_readings_pkey PRIMARY KEY (id);


--
-- Name: vehicle_statuses vehicle_statuses_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_statuses
    ADD CONSTRAINT vehicle_statuses_name_unique UNIQUE (name);


--
-- Name: vehicle_statuses vehicle_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_statuses
    ADD CONSTRAINT vehicle_statuses_pkey PRIMARY KEY (id);


--
-- Name: vehicle_types vehicle_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_types
    ADD CONSTRAINT vehicle_types_name_unique UNIQUE (name);


--
-- Name: vehicle_types vehicle_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_types
    ADD CONSTRAINT vehicle_types_pkey PRIMARY KEY (id);


--
-- Name: vehicles vehicles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_pkey PRIMARY KEY (id);


--
-- Name: vehicles vehicles_registration_plate_organization_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_registration_plate_organization_unique UNIQUE (registration_plate, organization_id);


--
-- Name: vehicles vehicles_vin_organization_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_vin_organization_unique UNIQUE (vin, organization_id);


--
-- Name: algeria_communes_wilaya_code_name_fr_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX algeria_communes_wilaya_code_name_fr_index ON public.algeria_communes USING btree (wilaya_code, name_fr);


--
-- Name: assignments_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX assignments_status_index ON public.assignments USING btree (status);


--
-- Name: idx_audit_business; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_business ON ONLY public.comprehensive_audit_logs USING gin (business_context);


--
-- Name: audit_logs_2025_04_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_business_context_idx ON public.audit_logs_2025_04 USING gin (business_context);


--
-- Name: idx_audit_compliance; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_compliance ON ONLY public.comprehensive_audit_logs USING gin (compliance_tags);


--
-- Name: audit_logs_2025_04_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_compliance_tags_idx ON public.audit_logs_2025_04 USING gin (compliance_tags);


--
-- Name: idx_audit_events; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_events ON ONLY public.comprehensive_audit_logs USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_04_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_event_category_event_type_event_action_idx ON public.audit_logs_2025_04 USING btree (event_category, event_type, event_action);


--
-- Name: idx_audit_org_occurred; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_org_occurred ON ONLY public.comprehensive_audit_logs USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_04_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_organization_id_occurred_at_idx ON public.audit_logs_2025_04 USING btree (organization_id, occurred_at DESC);


--
-- Name: idx_audit_request; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_request ON ONLY public.comprehensive_audit_logs USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_04_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_request_id_idx ON public.audit_logs_2025_04 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: idx_audit_resource; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_resource ON ONLY public.comprehensive_audit_logs USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_04_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_04 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: idx_audit_risk; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_risk ON ONLY public.comprehensive_audit_logs USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_04_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_risk_level_occurred_at_idx ON public.audit_logs_2025_04 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: idx_audit_user_occurred; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_user_occurred ON ONLY public.comprehensive_audit_logs USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_04_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_user_id_occurred_at_idx ON public.audit_logs_2025_04 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: idx_audit_uuid; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_audit_uuid ON ONLY public.comprehensive_audit_logs USING btree (uuid);


--
-- Name: audit_logs_2025_04_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_04_uuid_idx ON public.audit_logs_2025_04 USING btree (uuid);


--
-- Name: audit_logs_2025_05_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_business_context_idx ON public.audit_logs_2025_05 USING gin (business_context);


--
-- Name: audit_logs_2025_05_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_compliance_tags_idx ON public.audit_logs_2025_05 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_05_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_event_category_event_type_event_action_idx ON public.audit_logs_2025_05 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_05_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_organization_id_occurred_at_idx ON public.audit_logs_2025_05 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_05_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_request_id_idx ON public.audit_logs_2025_05 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_05_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_05 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_05_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_risk_level_occurred_at_idx ON public.audit_logs_2025_05 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_05_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_user_id_occurred_at_idx ON public.audit_logs_2025_05 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_05_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_05_uuid_idx ON public.audit_logs_2025_05 USING btree (uuid);


--
-- Name: audit_logs_2025_06_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_business_context_idx ON public.audit_logs_2025_06 USING gin (business_context);


--
-- Name: audit_logs_2025_06_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_compliance_tags_idx ON public.audit_logs_2025_06 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_06_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_event_category_event_type_event_action_idx ON public.audit_logs_2025_06 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_06_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_organization_id_occurred_at_idx ON public.audit_logs_2025_06 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_06_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_request_id_idx ON public.audit_logs_2025_06 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_06_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_06 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_06_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_risk_level_occurred_at_idx ON public.audit_logs_2025_06 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_06_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_user_id_occurred_at_idx ON public.audit_logs_2025_06 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_06_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_06_uuid_idx ON public.audit_logs_2025_06 USING btree (uuid);


--
-- Name: audit_logs_2025_07_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_business_context_idx ON public.audit_logs_2025_07 USING gin (business_context);


--
-- Name: audit_logs_2025_07_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_compliance_tags_idx ON public.audit_logs_2025_07 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_07_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_event_category_event_type_event_action_idx ON public.audit_logs_2025_07 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_07_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_organization_id_occurred_at_idx ON public.audit_logs_2025_07 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_07_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_request_id_idx ON public.audit_logs_2025_07 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_07_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_07 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_07_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_risk_level_occurred_at_idx ON public.audit_logs_2025_07 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_07_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_user_id_occurred_at_idx ON public.audit_logs_2025_07 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_07_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_07_uuid_idx ON public.audit_logs_2025_07 USING btree (uuid);


--
-- Name: audit_logs_2025_08_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_business_context_idx ON public.audit_logs_2025_08 USING gin (business_context);


--
-- Name: audit_logs_2025_08_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_compliance_tags_idx ON public.audit_logs_2025_08 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_08_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_event_category_event_type_event_action_idx ON public.audit_logs_2025_08 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_08_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_organization_id_occurred_at_idx ON public.audit_logs_2025_08 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_08_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_request_id_idx ON public.audit_logs_2025_08 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_08_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_08 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_08_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_risk_level_occurred_at_idx ON public.audit_logs_2025_08 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_08_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_user_id_occurred_at_idx ON public.audit_logs_2025_08 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_08_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_08_uuid_idx ON public.audit_logs_2025_08 USING btree (uuid);


--
-- Name: audit_logs_2025_09_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_business_context_idx ON public.audit_logs_2025_09 USING gin (business_context);


--
-- Name: audit_logs_2025_09_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_compliance_tags_idx ON public.audit_logs_2025_09 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_09_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_event_category_event_type_event_action_idx ON public.audit_logs_2025_09 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_09_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_organization_id_occurred_at_idx ON public.audit_logs_2025_09 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_09_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_request_id_idx ON public.audit_logs_2025_09 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_09_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_09 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_09_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_risk_level_occurred_at_idx ON public.audit_logs_2025_09 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_09_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_user_id_occurred_at_idx ON public.audit_logs_2025_09 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_09_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_09_uuid_idx ON public.audit_logs_2025_09 USING btree (uuid);


--
-- Name: audit_logs_2025_10_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_business_context_idx ON public.audit_logs_2025_10 USING gin (business_context);


--
-- Name: audit_logs_2025_10_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_compliance_tags_idx ON public.audit_logs_2025_10 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_10_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_event_category_event_type_event_action_idx ON public.audit_logs_2025_10 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_10_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_organization_id_occurred_at_idx ON public.audit_logs_2025_10 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_10_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_request_id_idx ON public.audit_logs_2025_10 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_10_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_10 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_10_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_risk_level_occurred_at_idx ON public.audit_logs_2025_10 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_10_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_user_id_occurred_at_idx ON public.audit_logs_2025_10 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_10_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_10_uuid_idx ON public.audit_logs_2025_10 USING btree (uuid);


--
-- Name: audit_logs_2025_11_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_business_context_idx ON public.audit_logs_2025_11 USING gin (business_context);


--
-- Name: audit_logs_2025_11_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_compliance_tags_idx ON public.audit_logs_2025_11 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_11_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_event_category_event_type_event_action_idx ON public.audit_logs_2025_11 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_11_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_organization_id_occurred_at_idx ON public.audit_logs_2025_11 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_11_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_request_id_idx ON public.audit_logs_2025_11 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_11_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_11 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_11_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_risk_level_occurred_at_idx ON public.audit_logs_2025_11 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_11_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_user_id_occurred_at_idx ON public.audit_logs_2025_11 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_11_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_11_uuid_idx ON public.audit_logs_2025_11 USING btree (uuid);


--
-- Name: audit_logs_2025_12_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_business_context_idx ON public.audit_logs_2025_12 USING gin (business_context);


--
-- Name: audit_logs_2025_12_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_compliance_tags_idx ON public.audit_logs_2025_12 USING gin (compliance_tags);


--
-- Name: audit_logs_2025_12_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_event_category_event_type_event_action_idx ON public.audit_logs_2025_12 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2025_12_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_organization_id_occurred_at_idx ON public.audit_logs_2025_12 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2025_12_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_request_id_idx ON public.audit_logs_2025_12 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2025_12_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2025_12 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2025_12_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_risk_level_occurred_at_idx ON public.audit_logs_2025_12 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2025_12_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_user_id_occurred_at_idx ON public.audit_logs_2025_12 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2025_12_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2025_12_uuid_idx ON public.audit_logs_2025_12 USING btree (uuid);


--
-- Name: audit_logs_2026_01_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_business_context_idx ON public.audit_logs_2026_01 USING gin (business_context);


--
-- Name: audit_logs_2026_01_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_compliance_tags_idx ON public.audit_logs_2026_01 USING gin (compliance_tags);


--
-- Name: audit_logs_2026_01_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_event_category_event_type_event_action_idx ON public.audit_logs_2026_01 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2026_01_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_organization_id_occurred_at_idx ON public.audit_logs_2026_01 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2026_01_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_request_id_idx ON public.audit_logs_2026_01 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2026_01_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2026_01 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2026_01_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_risk_level_occurred_at_idx ON public.audit_logs_2026_01 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2026_01_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_user_id_occurred_at_idx ON public.audit_logs_2026_01 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2026_01_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_01_uuid_idx ON public.audit_logs_2026_01 USING btree (uuid);


--
-- Name: audit_logs_2026_02_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_business_context_idx ON public.audit_logs_2026_02 USING gin (business_context);


--
-- Name: audit_logs_2026_02_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_compliance_tags_idx ON public.audit_logs_2026_02 USING gin (compliance_tags);


--
-- Name: audit_logs_2026_02_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_event_category_event_type_event_action_idx ON public.audit_logs_2026_02 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2026_02_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_organization_id_occurred_at_idx ON public.audit_logs_2026_02 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2026_02_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_request_id_idx ON public.audit_logs_2026_02 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2026_02_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2026_02 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2026_02_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_risk_level_occurred_at_idx ON public.audit_logs_2026_02 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2026_02_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_user_id_occurred_at_idx ON public.audit_logs_2026_02 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2026_02_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_02_uuid_idx ON public.audit_logs_2026_02 USING btree (uuid);


--
-- Name: audit_logs_2026_03_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_business_context_idx ON public.audit_logs_2026_03 USING gin (business_context);


--
-- Name: audit_logs_2026_03_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_compliance_tags_idx ON public.audit_logs_2026_03 USING gin (compliance_tags);


--
-- Name: audit_logs_2026_03_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_event_category_event_type_event_action_idx ON public.audit_logs_2026_03 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2026_03_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_organization_id_occurred_at_idx ON public.audit_logs_2026_03 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2026_03_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_request_id_idx ON public.audit_logs_2026_03 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2026_03_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2026_03 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2026_03_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_risk_level_occurred_at_idx ON public.audit_logs_2026_03 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2026_03_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_user_id_occurred_at_idx ON public.audit_logs_2026_03 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2026_03_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_03_uuid_idx ON public.audit_logs_2026_03 USING btree (uuid);


--
-- Name: audit_logs_2026_04_business_context_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_business_context_idx ON public.audit_logs_2026_04 USING gin (business_context);


--
-- Name: audit_logs_2026_04_compliance_tags_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_compliance_tags_idx ON public.audit_logs_2026_04 USING gin (compliance_tags);


--
-- Name: audit_logs_2026_04_event_category_event_type_event_action_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_event_category_event_type_event_action_idx ON public.audit_logs_2026_04 USING btree (event_category, event_type, event_action);


--
-- Name: audit_logs_2026_04_organization_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_organization_id_occurred_at_idx ON public.audit_logs_2026_04 USING btree (organization_id, occurred_at DESC);


--
-- Name: audit_logs_2026_04_request_id_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_request_id_idx ON public.audit_logs_2026_04 USING btree (request_id) WHERE (request_id IS NOT NULL);


--
-- Name: audit_logs_2026_04_resource_type_resource_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_resource_type_resource_id_occurred_at_idx ON public.audit_logs_2026_04 USING btree (resource_type, resource_id, occurred_at DESC);


--
-- Name: audit_logs_2026_04_risk_level_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_risk_level_occurred_at_idx ON public.audit_logs_2026_04 USING btree (risk_level, occurred_at DESC) WHERE ((risk_level)::text = ANY ((ARRAY['high'::character varying, 'critical'::character varying])::text[]));


--
-- Name: audit_logs_2026_04_user_id_occurred_at_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_user_id_occurred_at_idx ON public.audit_logs_2026_04 USING btree (user_id, occurred_at DESC) WHERE (user_id IS NOT NULL);


--
-- Name: audit_logs_2026_04_uuid_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX audit_logs_2026_04_uuid_idx ON public.audit_logs_2026_04 USING btree (uuid);


--
-- Name: contextual_permissions_organization_id_permission_name_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX contextual_permissions_organization_id_permission_name_index ON public.contextual_permissions USING btree (organization_id, permission_name);


--
-- Name: contextual_permissions_valid_until_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX contextual_permissions_valid_until_index ON public.contextual_permissions USING btree (valid_until);


--
-- Name: daily_metrics_organization_id_metric_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX daily_metrics_organization_id_metric_date_index ON public.daily_metrics USING btree (organization_id, metric_date);


--
-- Name: document_revisions_document_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX document_revisions_document_id_index ON public.document_revisions USING btree (document_id);


--
-- Name: document_revisions_document_id_revision_number_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX document_revisions_document_id_revision_number_index ON public.document_revisions USING btree (document_id, revision_number);


--
-- Name: documentables_documentable_type_documentable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documentables_documentable_type_documentable_id_index ON public.documentables USING btree (documentable_type, documentable_id);


--
-- Name: documents_document_category_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_document_category_id_index ON public.documents USING btree (document_category_id);


--
-- Name: documents_extra_metadata_gin; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_extra_metadata_gin ON public.documents USING gin (extra_metadata jsonb_path_ops);


--
-- Name: documents_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_organization_id_index ON public.documents USING btree (organization_id);


--
-- Name: documents_search_vector_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_search_vector_idx ON public.documents USING gin (search_vector);


--
-- Name: documents_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_status_index ON public.documents USING btree (status);


--
-- Name: driver_statuses_active_default; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_active_default ON public.driver_statuses USING btree (is_active, is_default);


--
-- Name: driver_statuses_assignments_available; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_assignments_available ON public.driver_statuses USING btree (allows_assignments, is_available_for_work);


--
-- Name: driver_statuses_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_is_active_index ON public.driver_statuses USING btree (is_active);


--
-- Name: driver_statuses_is_default_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_is_default_index ON public.driver_statuses USING btree (is_default);


--
-- Name: driver_statuses_org_active_sort; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_org_active_sort ON public.driver_statuses USING btree (organization_id, is_active, sort_order);


--
-- Name: driver_statuses_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_organization_id_index ON public.driver_statuses USING btree (organization_id);


--
-- Name: driver_statuses_slug_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_slug_idx ON public.driver_statuses USING btree (slug);


--
-- Name: driver_statuses_sort_order_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_sort_order_idx ON public.driver_statuses USING btree (sort_order);


--
-- Name: driver_statuses_sort_order_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX driver_statuses_sort_order_index ON public.driver_statuses USING btree (sort_order);


--
-- Name: expense_audit_logs_action_created_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_action_created_at_index ON public.expense_audit_logs USING btree (action, created_at);


--
-- Name: expense_audit_logs_ip_address_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_ip_address_index ON public.expense_audit_logs USING btree (ip_address);


--
-- Name: expense_audit_logs_is_anomaly_risk_level_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_is_anomaly_risk_level_index ON public.expense_audit_logs USING btree (is_anomaly, risk_level);


--
-- Name: expense_audit_logs_organization_id_action_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_organization_id_action_index ON public.expense_audit_logs USING btree (organization_id, action);


--
-- Name: expense_audit_logs_organization_id_created_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_organization_id_created_at_index ON public.expense_audit_logs USING btree (organization_id, created_at);


--
-- Name: expense_audit_logs_organization_id_user_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_organization_id_user_id_index ON public.expense_audit_logs USING btree (organization_id, user_id);


--
-- Name: expense_audit_logs_organization_id_vehicle_expense_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_organization_id_vehicle_expense_id_index ON public.expense_audit_logs USING btree (organization_id, vehicle_expense_id);


--
-- Name: expense_audit_logs_requires_review_reviewed_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_requires_review_reviewed_index ON public.expense_audit_logs USING btree (requires_review, reviewed);


--
-- Name: expense_audit_logs_session_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_session_id_index ON public.expense_audit_logs USING btree (session_id);


--
-- Name: expense_audit_logs_vehicle_expense_id_created_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_audit_logs_vehicle_expense_id_created_at_index ON public.expense_audit_logs USING btree (vehicle_expense_id, created_at);


--
-- Name: expense_budgets_expense_category_budget_year_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_budgets_expense_category_budget_year_index ON public.expense_budgets USING btree (expense_category, budget_year);


--
-- Name: expense_budgets_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_budgets_is_active_index ON public.expense_budgets USING btree (is_active);


--
-- Name: expense_budgets_organization_id_budget_period_budget_year_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_budgets_organization_id_budget_period_budget_year_index ON public.expense_budgets USING btree (organization_id, budget_period, budget_year);


--
-- Name: expense_budgets_vehicle_id_budget_year_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_budgets_vehicle_id_budget_year_index ON public.expense_budgets USING btree (vehicle_id, budget_year);


--
-- Name: expense_groups_budget_remaining_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_groups_budget_remaining_index ON public.expense_groups USING btree (budget_remaining);


--
-- Name: expense_groups_created_by_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_groups_created_by_index ON public.expense_groups USING btree (created_by);


--
-- Name: expense_groups_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_groups_is_active_index ON public.expense_groups USING btree (is_active);


--
-- Name: expense_groups_organization_id_fiscal_year_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_groups_organization_id_fiscal_year_index ON public.expense_groups USING btree (organization_id, fiscal_year);


--
-- Name: expense_groups_organization_id_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX expense_groups_organization_id_is_active_index ON public.expense_groups USING btree (organization_id, is_active);


--
-- Name: granular_permissions_module_resource_action_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX granular_permissions_module_resource_action_index ON public.granular_permissions USING btree (module, resource, action);


--
-- Name: granular_permissions_scope_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX granular_permissions_scope_is_active_index ON public.granular_permissions USING btree (scope, is_active);


--
-- Name: idx_assignments_dates_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_dates_org ON public.assignments USING btree (start_datetime, end_datetime, organization_id);


--
-- Name: idx_assignments_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_organization ON public.assignments USING btree (organization_id);


--
-- Name: idx_dah_action; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_dah_action ON public.depot_assignment_history USING btree (action);


--
-- Name: idx_dah_depot_assigned; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_dah_depot_assigned ON public.depot_assignment_history USING btree (depot_id, assigned_at);


--
-- Name: idx_dah_org_assigned; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_dah_org_assigned ON public.depot_assignment_history USING btree (organization_id, assigned_at);


--
-- Name: idx_dah_vehicle_assigned; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_dah_vehicle_assigned ON public.depot_assignment_history USING btree (vehicle_id, assigned_at);


--
-- Name: idx_driver_period; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_driver_period ON public.assignments USING btree (driver_id, start_datetime, end_datetime);


--
-- Name: idx_drivers_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_drivers_organization ON public.drivers USING btree (organization_id);


--
-- Name: idx_drivers_supervisor_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_drivers_supervisor_org ON public.drivers USING btree (supervisor_id, organization_id);


--
-- Name: idx_history_action; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_action ON public.driver_sanction_histories USING btree (action);


--
-- Name: idx_history_created; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_created ON public.driver_sanction_histories USING btree (created_at);


--
-- Name: idx_history_sanction; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_sanction ON public.driver_sanction_histories USING btree (sanction_id);


--
-- Name: idx_history_sanction_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_sanction_date ON public.driver_sanction_histories USING btree (sanction_id, created_at);


--
-- Name: idx_history_user; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_user ON public.driver_sanction_histories USING btree (user_id);


--
-- Name: idx_history_user_action; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_history_user_action ON public.driver_sanction_histories USING btree (user_id, action);


--
-- Name: idx_maintenance_alerts_acknowledged; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_acknowledged ON public.maintenance_alerts USING btree (is_acknowledged);


--
-- Name: idx_maintenance_alerts_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_active ON public.maintenance_alerts USING btree (is_acknowledged, priority, created_at);


--
-- Name: idx_maintenance_alerts_dashboard; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_dashboard ON public.maintenance_alerts USING btree (organization_id, is_acknowledged, priority);


--
-- Name: idx_maintenance_alerts_due; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_due ON public.maintenance_alerts USING btree (organization_id, due_date, is_acknowledged);


--
-- Name: idx_maintenance_alerts_due_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_due_date ON public.maintenance_alerts USING btree (due_date);


--
-- Name: idx_maintenance_alerts_due_mileage; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_due_mileage ON public.maintenance_alerts USING btree (due_mileage);


--
-- Name: idx_maintenance_alerts_escalation; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_escalation ON public.maintenance_alerts USING btree (priority, created_at, is_acknowledged);


--
-- Name: idx_maintenance_alerts_priority; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_priority ON public.maintenance_alerts USING btree (priority);


--
-- Name: idx_maintenance_alerts_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_alerts_type ON public.maintenance_alerts USING btree (alert_type);


--
-- Name: idx_maintenance_documents_op_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_documents_op_type ON public.maintenance_documents USING btree (maintenance_operation_id, document_type);


--
-- Name: idx_maintenance_documents_org_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_documents_org_type ON public.maintenance_documents USING btree (organization_id, file_type);


--
-- Name: idx_maintenance_documents_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_documents_type ON public.maintenance_documents USING btree (document_type);


--
-- Name: idx_maintenance_logs_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_logs_organization ON public.maintenance_logs USING btree (organization_id);


--
-- Name: idx_maintenance_operations_completed; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_completed ON public.maintenance_operations USING btree (completed_date);


--
-- Name: idx_maintenance_operations_completed_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_completed_status ON public.maintenance_operations USING btree (completed_date, status);


--
-- Name: idx_maintenance_operations_date_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_date_status ON public.maintenance_operations USING btree (scheduled_date, status);


--
-- Name: idx_maintenance_operations_org_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_org_status ON public.maintenance_operations USING btree (organization_id, status, scheduled_date);


--
-- Name: idx_maintenance_operations_reporting; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_reporting ON public.maintenance_operations USING btree (organization_id, completed_date, total_cost);


--
-- Name: idx_maintenance_operations_scheduled; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_scheduled ON public.maintenance_operations USING btree (scheduled_date);


--
-- Name: idx_maintenance_operations_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_status ON public.maintenance_operations USING btree (status);


--
-- Name: idx_maintenance_operations_vehicle_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_operations_vehicle_status ON public.maintenance_operations USING btree (organization_id, vehicle_id, status);


--
-- Name: idx_maintenance_plans_next_due_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_plans_next_due_date ON public.maintenance_plans USING btree (next_due_date, organization_id);


--
-- Name: idx_maintenance_plans_next_due_mileage; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_plans_next_due_mileage ON public.maintenance_plans USING btree (next_due_mileage, organization_id);


--
-- Name: idx_maintenance_plans_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_plans_organization ON public.maintenance_plans USING btree (organization_id);


--
-- Name: idx_maintenance_providers_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_providers_active ON public.maintenance_providers USING btree (is_active);


--
-- Name: idx_maintenance_providers_email; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_providers_email ON public.maintenance_providers USING btree (email);


--
-- Name: idx_maintenance_providers_org_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_providers_org_active ON public.maintenance_providers USING btree (organization_id, is_active);


--
-- Name: idx_maintenance_providers_org_city; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_providers_org_city ON public.maintenance_providers USING btree (organization_id, city);


--
-- Name: idx_maintenance_providers_phone; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_providers_phone ON public.maintenance_providers USING btree (phone);


--
-- Name: idx_maintenance_schedules_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_active ON public.maintenance_schedules USING btree (is_active);


--
-- Name: idx_maintenance_schedules_alerts; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_alerts ON public.maintenance_schedules USING btree (organization_id, is_active, next_due_date);


--
-- Name: idx_maintenance_schedules_due_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_due_active ON public.maintenance_schedules USING btree (next_due_date, is_active);


--
-- Name: idx_maintenance_schedules_due_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_due_date ON public.maintenance_schedules USING btree (next_due_date);


--
-- Name: idx_maintenance_schedules_due_mileage; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_due_mileage ON public.maintenance_schedules USING btree (next_due_mileage);


--
-- Name: idx_maintenance_schedules_vehicle_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_schedules_vehicle_active ON public.maintenance_schedules USING btree (organization_id, vehicle_id, is_active);


--
-- Name: idx_maintenance_types_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_types_active ON public.maintenance_types USING btree (is_active);


--
-- Name: idx_maintenance_types_category; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_types_category ON public.maintenance_types USING btree (category);


--
-- Name: idx_maintenance_types_composite; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_types_composite ON public.maintenance_types USING btree (organization_id, is_active, category);


--
-- Name: idx_maintenance_types_name; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_types_name ON public.maintenance_types USING btree (name);


--
-- Name: idx_mileage_readings_method; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_method ON public.vehicle_mileage_readings USING btree (recording_method);


--
-- Name: idx_mileage_readings_org_vehicle_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_org_vehicle_date ON public.vehicle_mileage_readings USING btree (organization_id, vehicle_id, recorded_at);


--
-- Name: idx_mileage_readings_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_organization ON public.vehicle_mileage_readings USING btree (organization_id);


--
-- Name: idx_mileage_readings_recorded_at; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_recorded_at ON public.vehicle_mileage_readings USING btree (recorded_at);


--
-- Name: idx_mileage_readings_recorded_by; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_recorded_by ON public.vehicle_mileage_readings USING btree (recorded_by_id);


--
-- Name: idx_mileage_readings_vehicle; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_vehicle ON public.vehicle_mileage_readings USING btree (vehicle_id);


--
-- Name: idx_mileage_readings_vehicle_chronology; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_mileage_readings_vehicle_chronology ON public.vehicle_mileage_readings USING btree (vehicle_id, recorded_at, mileage);


--
-- Name: idx_org_status_start; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_org_status_start ON public.assignments USING btree (organization_id, status, start_datetime);


--
-- Name: idx_organizations_location; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_organizations_location ON public.organizations USING btree (city, wilaya);


--
-- Name: idx_organizations_name; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_organizations_name ON public.organizations USING btree (name);


--
-- Name: idx_organizations_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_organizations_status ON public.organizations USING btree (status);


--
-- Name: idx_organizations_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_organizations_type ON public.organizations USING btree (organization_type);


--
-- Name: idx_period_range; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_period_range ON public.assignments USING btree (start_datetime, end_datetime);


--
-- Name: idx_repair_categories_org_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_categories_org_active ON public.repair_categories USING btree (organization_id, is_active);


--
-- Name: idx_repair_categories_sort; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_categories_sort ON public.repair_categories USING btree (sort_order);


--
-- Name: idx_repair_history_request_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_history_request_date ON public.repair_request_history USING btree (repair_request_id, created_at);


--
-- Name: idx_repair_notif_user_unread; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_notif_user_unread ON public.repair_notifications USING btree (user_id, is_read, created_at);


--
-- Name: idx_repair_requests_category_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_requests_category_org ON public.repair_requests USING btree (category_id, organization_id);


--
-- Name: idx_repair_requests_driver_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_requests_driver_status ON public.repair_requests USING btree (driver_id, status);


--
-- Name: idx_repair_requests_status_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_requests_status_org ON public.repair_requests USING btree (status, organization_id);


--
-- Name: idx_repair_requests_urgency; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_requests_urgency ON public.repair_requests USING btree (urgency, status);


--
-- Name: idx_repair_requests_vehicle_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_repair_requests_vehicle_date ON public.repair_requests USING btree (vehicle_id, created_at);


--
-- Name: idx_sanctions_archived; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_archived ON public.driver_sanctions USING btree (archived_at);


--
-- Name: idx_sanctions_date; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_date ON public.driver_sanctions USING btree (sanction_date);


--
-- Name: idx_sanctions_driver; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_driver ON public.driver_sanctions USING btree (driver_id);


--
-- Name: idx_sanctions_org_archived; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_org_archived ON public.driver_sanctions USING btree (organization_id, archived_at);


--
-- Name: idx_sanctions_org_driver; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_org_driver ON public.driver_sanctions USING btree (organization_id, driver_id);


--
-- Name: idx_sanctions_org_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_org_status ON public.driver_sanctions USING btree (organization_id, status);


--
-- Name: idx_sanctions_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_organization ON public.driver_sanctions USING btree (organization_id);


--
-- Name: idx_sanctions_severity; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_severity ON public.driver_sanctions USING btree (severity);


--
-- Name: idx_sanctions_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_status ON public.driver_sanctions USING btree (status);


--
-- Name: idx_sanctions_supervisor; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_supervisor ON public.driver_sanctions USING btree (supervisor_id);


--
-- Name: idx_sanctions_type; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_sanctions_type ON public.driver_sanctions USING btree (sanction_type);


--
-- Name: idx_suppliers_auto_score; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_suppliers_auto_score ON public.suppliers USING btree (auto_score_enabled);


--
-- Name: idx_suppliers_rating_status; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_suppliers_rating_status ON public.suppliers USING btree (rating, is_active, blacklisted);


--
-- Name: idx_suppliers_scores; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_suppliers_scores ON public.suppliers USING btree (rating, quality_score, reliability_score);


--
-- Name: idx_suppliers_scores_perf; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_suppliers_scores_perf ON public.suppliers USING btree (quality_score, reliability_score);


--
-- Name: idx_users_email_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_email_deleted ON public.users USING btree (email, deleted_at);


--
-- Name: idx_users_org_status_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_org_status_deleted ON public.users USING btree (organization_id, status, deleted_at);


--
-- Name: idx_vehicle_categories_org_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicle_categories_org_active ON public.vehicle_categories USING btree (organization_id, is_active);


--
-- Name: idx_vehicle_depots_org_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicle_depots_org_active ON public.vehicle_depots USING btree (organization_id, is_active);


--
-- Name: idx_vehicle_period; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicle_period ON public.assignments USING btree (vehicle_id, start_datetime, end_datetime);


--
-- Name: idx_vehicles_archived; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_archived ON public.vehicles USING btree (is_archived);


--
-- Name: idx_vehicles_category_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_category_org ON public.vehicles USING btree (category_id, organization_id);


--
-- Name: idx_vehicles_depot_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_depot_org ON public.vehicles USING btree (depot_id, organization_id);


--
-- Name: idx_vehicles_name; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_name ON public.vehicles USING btree (vehicle_name);


--
-- Name: idx_vehicles_org_archived; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_org_archived ON public.vehicles USING btree (organization_id, is_archived);


--
-- Name: idx_vehicles_org_plate; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_org_plate ON public.vehicles USING btree (organization_id, registration_plate) WHERE (deleted_at IS NULL);


--
-- Name: idx_vehicles_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_organization ON public.vehicles USING btree (organization_id);


--
-- Name: idx_vehicles_registration_plate; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_registration_plate ON public.vehicles USING btree (registration_plate);


--
-- Name: idx_vehicles_status_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_status_org ON public.vehicles USING btree (status_id, organization_id);


--
-- Name: idx_vehicles_vin; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_vin ON public.vehicles USING btree (vin) WHERE (vin IS NOT NULL);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_permissions_team_foreign_key_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_team_foreign_key_index ON public.model_has_permissions USING btree (organization_id);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: model_has_roles_team_foreign_key_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_team_foreign_key_index ON public.model_has_roles USING btree (organization_id);


--
-- Name: organization_metrics_metric_date_metric_period_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organization_metrics_metric_date_metric_period_index ON public.organization_metrics USING btree (metric_date, metric_period);


--
-- Name: organizations_city_wilaya_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_city_wilaya_index ON public.organizations USING btree (city, wilaya);


--
-- Name: organizations_is_tenant_root_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_is_tenant_root_status_index ON public.organizations USING btree (is_tenant_root, status);


--
-- Name: organizations_last_activity_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_last_activity_at_index ON public.organizations USING btree (last_activity_at);


--
-- Name: organizations_name_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_name_index ON public.organizations USING btree (name);


--
-- Name: organizations_organization_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_organization_type_index ON public.organizations USING btree (organization_type);


--
-- Name: organizations_parent_organization_id_hierarchy_depth_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_parent_organization_id_hierarchy_depth_index ON public.organizations USING btree (parent_organization_id, hierarchy_depth);


--
-- Name: organizations_parent_organization_id_hierarchy_level_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_parent_organization_id_hierarchy_level_index ON public.organizations USING btree (parent_organization_id, hierarchy_level);


--
-- Name: organizations_slug_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_slug_index ON public.organizations USING btree (slug);


--
-- Name: organizations_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_status_index ON public.organizations USING btree (status);


--
-- Name: organizations_status_subscription_expires_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_status_subscription_expires_at_index ON public.organizations USING btree (status, subscription_expires_at);


--
-- Name: organizations_subscription_plan_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_subscription_plan_status_index ON public.organizations USING btree (subscription_plan, status);


--
-- Name: permissions_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX permissions_organization_id_index ON public.permissions USING btree (organization_id);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: repair_requests_manager_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_manager_id_index ON public.repair_requests USING btree (manager_id);


--
-- Name: repair_requests_organization_id_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_organization_id_status_index ON public.repair_requests USING btree (organization_id, status);


--
-- Name: repair_requests_priority_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_priority_status_index ON public.repair_requests USING btree (priority, status);


--
-- Name: repair_requests_requested_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_requested_at_index ON public.repair_requests USING btree (requested_at);


--
-- Name: repair_requests_supervisor_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_supervisor_id_index ON public.repair_requests USING btree (supervisor_id);


--
-- Name: repair_requests_vehicle_id_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX repair_requests_vehicle_id_status_index ON public.repair_requests USING btree (vehicle_id, status);


--
-- Name: roles_name_guard_null_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX roles_name_guard_null_organization_unique ON public.roles USING btree (name, guard_name) WHERE (organization_id IS NULL);


--
-- Name: roles_name_guard_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX roles_name_guard_organization_unique ON public.roles USING btree (name, guard_name, organization_id);


--
-- Name: roles_team_foreign_key_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX roles_team_foreign_key_index ON public.roles USING btree (organization_id);


--
-- Name: subscription_changes_change_type_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX subscription_changes_change_type_status_index ON public.subscription_changes USING btree (change_type, status);


--
-- Name: subscription_changes_organization_id_effective_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX subscription_changes_organization_id_effective_date_index ON public.subscription_changes USING btree (organization_id, effective_date);


--
-- Name: subscription_plans_is_public_sort_order_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX subscription_plans_is_public_sort_order_index ON public.subscription_plans USING btree (is_public, sort_order);


--
-- Name: subscription_plans_tier_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX subscription_plans_tier_is_active_index ON public.subscription_plans USING btree (tier, is_active);


--
-- Name: supervisor_driver_assignments_supervisor_id_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX supervisor_driver_assignments_supervisor_id_is_active_index ON public.supervisor_driver_assignments USING btree (supervisor_id, is_active);


--
-- Name: supplier_ratings_organization_id_supplier_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX supplier_ratings_organization_id_supplier_id_index ON public.supplier_ratings USING btree (organization_id, supplier_id);


--
-- Name: supplier_ratings_repair_request_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX supplier_ratings_repair_request_id_index ON public.supplier_ratings USING btree (repair_request_id);


--
-- Name: supplier_ratings_supplier_id_overall_rating_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX supplier_ratings_supplier_id_overall_rating_index ON public.supplier_ratings USING btree (supplier_id, overall_rating);


--
-- Name: suppliers_blacklisted_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_blacklisted_index ON public.suppliers USING btree (blacklisted);


--
-- Name: suppliers_city_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_city_index ON public.suppliers USING btree (city);


--
-- Name: suppliers_company_name_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_company_name_index ON public.suppliers USING btree (company_name);


--
-- Name: suppliers_contact_phone_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_contact_phone_index ON public.suppliers USING btree (contact_phone);


--
-- Name: suppliers_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_is_active_index ON public.suppliers USING btree (is_active);


--
-- Name: suppliers_is_preferred_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_is_preferred_index ON public.suppliers USING btree (is_preferred);


--
-- Name: suppliers_organization_id_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_organization_id_is_active_index ON public.suppliers USING btree (organization_id, is_active);


--
-- Name: suppliers_organization_id_is_preferred_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_organization_id_is_preferred_index ON public.suppliers USING btree (organization_id, is_preferred);


--
-- Name: suppliers_organization_id_supplier_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_organization_id_supplier_type_index ON public.suppliers USING btree (organization_id, supplier_type);


--
-- Name: suppliers_rating_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_rating_index ON public.suppliers USING btree (rating);


--
-- Name: suppliers_rating_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_rating_is_active_index ON public.suppliers USING btree (rating, is_active);


--
-- Name: suppliers_search_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_search_idx ON public.suppliers USING gin (to_tsvector('french'::regconfig, (((((company_name)::text || ' '::text) || (COALESCE(contact_first_name, ''::character varying))::text) || ' '::text) || (COALESCE(contact_last_name, ''::character varying))::text)));


--
-- Name: suppliers_wilaya_city_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_wilaya_city_index ON public.suppliers USING btree (wilaya, city);


--
-- Name: suppliers_wilaya_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX suppliers_wilaya_index ON public.suppliers USING btree (wilaya);


--
-- Name: user_organizations_expires_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX user_organizations_expires_at_index ON public.user_organizations USING btree (expires_at);


--
-- Name: user_organizations_organization_id_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX user_organizations_organization_id_is_active_index ON public.user_organizations USING btree (organization_id, is_active);


--
-- Name: user_organizations_user_id_is_primary_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX user_organizations_user_id_is_primary_index ON public.user_organizations USING btree (user_id, is_primary);


--
-- Name: user_vehicle_assignments_supervisor_id_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX user_vehicle_assignments_supervisor_id_is_active_index ON public.user_vehicle_assignments USING btree (supervisor_id, is_active);


--
-- Name: users_employee_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_employee_id_index ON public.users USING btree (employee_id);


--
-- Name: users_is_super_admin_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_is_super_admin_index ON public.users USING btree (is_super_admin);


--
-- Name: users_last_activity_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_last_activity_at_index ON public.users USING btree (last_activity_at);


--
-- Name: users_manager_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_manager_id_index ON public.users USING btree (manager_id);


--
-- Name: users_organization_id_user_status_is_active_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_organization_id_user_status_is_active_index ON public.users USING btree (organization_id, user_status, is_active);


--
-- Name: users_supervisor_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX users_supervisor_id_index ON public.users USING btree (supervisor_id);


--
-- Name: vehicle_expenses_approval_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_approval_status_index ON public.vehicle_expenses USING btree (approval_status);


--
-- Name: vehicle_expenses_approved_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_approved_expense_date_index ON public.vehicle_expenses USING btree (approved, expense_date);


--
-- Name: vehicle_expenses_approved_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_approved_index ON public.vehicle_expenses USING btree (approved);


--
-- Name: vehicle_expenses_cost_center_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_cost_center_index ON public.vehicle_expenses USING btree (cost_center);


--
-- Name: vehicle_expenses_expense_category_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_category_expense_date_index ON public.vehicle_expenses USING btree (expense_category, expense_date);


--
-- Name: vehicle_expenses_expense_city_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_city_index ON public.vehicle_expenses USING btree (expense_city);


--
-- Name: vehicle_expenses_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_date_index ON public.vehicle_expenses USING btree (expense_date);


--
-- Name: vehicle_expenses_expense_group_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_group_id_index ON public.vehicle_expenses USING btree (expense_group_id);


--
-- Name: vehicle_expenses_expense_wilaya_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_wilaya_expense_date_index ON public.vehicle_expenses USING btree (expense_wilaya, expense_date);


--
-- Name: vehicle_expenses_expense_wilaya_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_expense_wilaya_index ON public.vehicle_expenses USING btree (expense_wilaya);


--
-- Name: vehicle_expenses_fuel_type_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_fuel_type_expense_date_index ON public.vehicle_expenses USING btree (fuel_type, expense_date);


--
-- Name: vehicle_expenses_invoice_number_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_invoice_number_index ON public.vehicle_expenses USING btree (invoice_number);


--
-- Name: vehicle_expenses_is_recurring_next_due_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_is_recurring_next_due_date_index ON public.vehicle_expenses USING btree (is_recurring, next_due_date);


--
-- Name: vehicle_expenses_is_urgent_approval_deadline_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_is_urgent_approval_deadline_index ON public.vehicle_expenses USING btree (is_urgent, approval_deadline);


--
-- Name: vehicle_expenses_location_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_location_idx ON public.vehicle_expenses USING btree (expense_latitude, expense_longitude) WHERE ((expense_latitude IS NOT NULL) AND (expense_longitude IS NOT NULL));


--
-- Name: vehicle_expenses_needs_approval_approved_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_needs_approval_approved_index ON public.vehicle_expenses USING btree (needs_approval, approved);


--
-- Name: vehicle_expenses_odometer_reading_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_odometer_reading_index ON public.vehicle_expenses USING btree (odometer_reading);


--
-- Name: vehicle_expenses_organization_id_approval_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_organization_id_approval_status_index ON public.vehicle_expenses USING btree (organization_id, approval_status);


--
-- Name: vehicle_expenses_organization_id_expense_category_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_organization_id_expense_category_index ON public.vehicle_expenses USING btree (organization_id, expense_category);


--
-- Name: vehicle_expenses_organization_id_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_organization_id_expense_date_index ON public.vehicle_expenses USING btree (organization_id, expense_date);


--
-- Name: vehicle_expenses_organization_id_expense_group_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_organization_id_expense_group_id_index ON public.vehicle_expenses USING btree (organization_id, expense_group_id);


--
-- Name: vehicle_expenses_payment_status_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_payment_status_expense_date_index ON public.vehicle_expenses USING btree (payment_status, expense_date);


--
-- Name: vehicle_expenses_priority_level_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_priority_level_index ON public.vehicle_expenses USING btree (priority_level);


--
-- Name: vehicle_expenses_requester_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_requester_id_index ON public.vehicle_expenses USING btree (requester_id);


--
-- Name: vehicle_expenses_search_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_search_idx ON public.vehicle_expenses USING gin (to_tsvector('french'::regconfig, ((description || ' '::text) || (expense_type)::text)));


--
-- Name: vehicle_expenses_supplier_id_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_supplier_id_expense_date_index ON public.vehicle_expenses USING btree (supplier_id, expense_date);


--
-- Name: vehicle_expenses_vehicle_id_expense_date_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicle_expenses_vehicle_id_expense_date_index ON public.vehicle_expenses USING btree (vehicle_id, expense_date);


--
-- Name: audit_logs_2025_04_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_04_business_context_idx;


--
-- Name: audit_logs_2025_04_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_04_compliance_tags_idx;


--
-- Name: audit_logs_2025_04_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_04_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_04_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_04_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_04_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_04_pkey;


--
-- Name: audit_logs_2025_04_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_04_request_id_idx;


--
-- Name: audit_logs_2025_04_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_04_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_04_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_04_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_04_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_04_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_04_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_04_uuid_idx;


--
-- Name: audit_logs_2025_05_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_05_business_context_idx;


--
-- Name: audit_logs_2025_05_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_05_compliance_tags_idx;


--
-- Name: audit_logs_2025_05_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_05_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_05_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_05_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_05_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_05_pkey;


--
-- Name: audit_logs_2025_05_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_05_request_id_idx;


--
-- Name: audit_logs_2025_05_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_05_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_05_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_05_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_05_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_05_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_05_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_05_uuid_idx;


--
-- Name: audit_logs_2025_06_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_06_business_context_idx;


--
-- Name: audit_logs_2025_06_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_06_compliance_tags_idx;


--
-- Name: audit_logs_2025_06_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_06_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_06_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_06_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_06_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_06_pkey;


--
-- Name: audit_logs_2025_06_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_06_request_id_idx;


--
-- Name: audit_logs_2025_06_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_06_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_06_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_06_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_06_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_06_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_06_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_06_uuid_idx;


--
-- Name: audit_logs_2025_07_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_07_business_context_idx;


--
-- Name: audit_logs_2025_07_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_07_compliance_tags_idx;


--
-- Name: audit_logs_2025_07_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_07_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_07_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_07_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_07_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_07_pkey;


--
-- Name: audit_logs_2025_07_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_07_request_id_idx;


--
-- Name: audit_logs_2025_07_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_07_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_07_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_07_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_07_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_07_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_07_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_07_uuid_idx;


--
-- Name: audit_logs_2025_08_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_08_business_context_idx;


--
-- Name: audit_logs_2025_08_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_08_compliance_tags_idx;


--
-- Name: audit_logs_2025_08_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_08_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_08_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_08_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_08_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_08_pkey;


--
-- Name: audit_logs_2025_08_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_08_request_id_idx;


--
-- Name: audit_logs_2025_08_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_08_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_08_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_08_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_08_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_08_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_08_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_08_uuid_idx;


--
-- Name: audit_logs_2025_09_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_09_business_context_idx;


--
-- Name: audit_logs_2025_09_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_09_compliance_tags_idx;


--
-- Name: audit_logs_2025_09_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_09_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_09_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_09_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_09_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_09_pkey;


--
-- Name: audit_logs_2025_09_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_09_request_id_idx;


--
-- Name: audit_logs_2025_09_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_09_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_09_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_09_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_09_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_09_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_09_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_09_uuid_idx;


--
-- Name: audit_logs_2025_10_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_10_business_context_idx;


--
-- Name: audit_logs_2025_10_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_10_compliance_tags_idx;


--
-- Name: audit_logs_2025_10_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_10_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_10_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_10_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_10_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_10_pkey;


--
-- Name: audit_logs_2025_10_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_10_request_id_idx;


--
-- Name: audit_logs_2025_10_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_10_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_10_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_10_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_10_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_10_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_10_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_10_uuid_idx;


--
-- Name: audit_logs_2025_11_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_11_business_context_idx;


--
-- Name: audit_logs_2025_11_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_11_compliance_tags_idx;


--
-- Name: audit_logs_2025_11_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_11_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_11_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_11_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_11_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_11_pkey;


--
-- Name: audit_logs_2025_11_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_11_request_id_idx;


--
-- Name: audit_logs_2025_11_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_11_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_11_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_11_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_11_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_11_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_11_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_11_uuid_idx;


--
-- Name: audit_logs_2025_12_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2025_12_business_context_idx;


--
-- Name: audit_logs_2025_12_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2025_12_compliance_tags_idx;


--
-- Name: audit_logs_2025_12_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2025_12_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2025_12_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2025_12_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2025_12_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2025_12_pkey;


--
-- Name: audit_logs_2025_12_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2025_12_request_id_idx;


--
-- Name: audit_logs_2025_12_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2025_12_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2025_12_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2025_12_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2025_12_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2025_12_user_id_occurred_at_idx;


--
-- Name: audit_logs_2025_12_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2025_12_uuid_idx;


--
-- Name: audit_logs_2026_01_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2026_01_business_context_idx;


--
-- Name: audit_logs_2026_01_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2026_01_compliance_tags_idx;


--
-- Name: audit_logs_2026_01_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2026_01_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2026_01_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2026_01_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2026_01_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2026_01_pkey;


--
-- Name: audit_logs_2026_01_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2026_01_request_id_idx;


--
-- Name: audit_logs_2026_01_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2026_01_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2026_01_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2026_01_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2026_01_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2026_01_user_id_occurred_at_idx;


--
-- Name: audit_logs_2026_01_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2026_01_uuid_idx;


--
-- Name: audit_logs_2026_02_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2026_02_business_context_idx;


--
-- Name: audit_logs_2026_02_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2026_02_compliance_tags_idx;


--
-- Name: audit_logs_2026_02_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2026_02_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2026_02_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2026_02_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2026_02_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2026_02_pkey;


--
-- Name: audit_logs_2026_02_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2026_02_request_id_idx;


--
-- Name: audit_logs_2026_02_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2026_02_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2026_02_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2026_02_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2026_02_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2026_02_user_id_occurred_at_idx;


--
-- Name: audit_logs_2026_02_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2026_02_uuid_idx;


--
-- Name: audit_logs_2026_03_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2026_03_business_context_idx;


--
-- Name: audit_logs_2026_03_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2026_03_compliance_tags_idx;


--
-- Name: audit_logs_2026_03_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2026_03_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2026_03_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2026_03_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2026_03_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2026_03_pkey;


--
-- Name: audit_logs_2026_03_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2026_03_request_id_idx;


--
-- Name: audit_logs_2026_03_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2026_03_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2026_03_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2026_03_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2026_03_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2026_03_user_id_occurred_at_idx;


--
-- Name: audit_logs_2026_03_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2026_03_uuid_idx;


--
-- Name: audit_logs_2026_04_business_context_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_business ATTACH PARTITION public.audit_logs_2026_04_business_context_idx;


--
-- Name: audit_logs_2026_04_compliance_tags_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_compliance ATTACH PARTITION public.audit_logs_2026_04_compliance_tags_idx;


--
-- Name: audit_logs_2026_04_event_category_event_type_event_action_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_events ATTACH PARTITION public.audit_logs_2026_04_event_category_event_type_event_action_idx;


--
-- Name: audit_logs_2026_04_organization_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_org_occurred ATTACH PARTITION public.audit_logs_2026_04_organization_id_occurred_at_idx;


--
-- Name: audit_logs_2026_04_pkey; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.pk_audit_logs ATTACH PARTITION public.audit_logs_2026_04_pkey;


--
-- Name: audit_logs_2026_04_request_id_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_request ATTACH PARTITION public.audit_logs_2026_04_request_id_idx;


--
-- Name: audit_logs_2026_04_resource_type_resource_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_resource ATTACH PARTITION public.audit_logs_2026_04_resource_type_resource_id_occurred_at_idx;


--
-- Name: audit_logs_2026_04_risk_level_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_risk ATTACH PARTITION public.audit_logs_2026_04_risk_level_occurred_at_idx;


--
-- Name: audit_logs_2026_04_user_id_occurred_at_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_user_occurred ATTACH PARTITION public.audit_logs_2026_04_user_id_occurred_at_idx;


--
-- Name: audit_logs_2026_04_uuid_idx; Type: INDEX ATTACH; Schema: public; Owner: zenfleet_user
--

ALTER INDEX public.idx_audit_uuid ATTACH PARTITION public.audit_logs_2026_04_uuid_idx;


--
-- Name: vehicle_expenses audit_expense_changes; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER audit_expense_changes AFTER INSERT OR DELETE OR UPDATE ON public.vehicle_expenses FOR EACH ROW EXECUTE FUNCTION public.log_expense_changes();


--
-- Name: vehicle_expenses auto_update_approval_status; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER auto_update_approval_status BEFORE INSERT OR UPDATE ON public.vehicle_expenses FOR EACH ROW EXECUTE FUNCTION public.update_approval_status();


--
-- Name: vehicle_expenses detect_anomalies_on_expense; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER detect_anomalies_on_expense AFTER INSERT OR UPDATE ON public.vehicle_expenses FOR EACH ROW EXECUTE FUNCTION public.detect_expense_anomalies();


--
-- Name: vehicle_mileage_readings trg_check_mileage_consistency; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER trg_check_mileage_consistency BEFORE INSERT ON public.vehicle_mileage_readings FOR EACH ROW EXECUTE FUNCTION public.check_mileage_consistency();


--
-- Name: organizations trg_organization_hierarchy; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER trg_organization_hierarchy BEFORE INSERT OR UPDATE ON public.organizations FOR EACH ROW EXECUTE FUNCTION public.update_organization_hierarchy();


--
-- Name: suppliers trigger_calculate_supplier_scores; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER trigger_calculate_supplier_scores BEFORE INSERT OR UPDATE ON public.suppliers FOR EACH ROW EXECUTE FUNCTION public.calculate_supplier_scores();


--
-- Name: vehicle_expenses update_group_budget_on_expense_delete; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER update_group_budget_on_expense_delete AFTER DELETE ON public.vehicle_expenses FOR EACH ROW WHEN ((old.expense_group_id IS NOT NULL)) EXECUTE FUNCTION public.update_expense_group_budget();


--
-- Name: vehicle_expenses update_group_budget_on_expense_insert; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER update_group_budget_on_expense_insert AFTER INSERT ON public.vehicle_expenses FOR EACH ROW WHEN ((new.expense_group_id IS NOT NULL)) EXECUTE FUNCTION public.update_expense_group_budget();


--
-- Name: vehicle_expenses update_group_budget_on_expense_update; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER update_group_budget_on_expense_update AFTER UPDATE ON public.vehicle_expenses FOR EACH ROW WHEN (((old.expense_group_id IS DISTINCT FROM new.expense_group_id) OR (old.total_ttc IS DISTINCT FROM new.total_ttc) OR (old.deleted_at IS DISTINCT FROM new.deleted_at))) EXECUTE FUNCTION public.update_expense_group_budget();


--
-- Name: algeria_communes algeria_communes_wilaya_code_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_communes
    ADD CONSTRAINT algeria_communes_wilaya_code_foreign FOREIGN KEY (wilaya_code) REFERENCES public.algeria_wilayas(code);


--
-- Name: assignments assignments_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: assignments assignments_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: assignments assignments_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE RESTRICT;


--
-- Name: assignments assignments_ended_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_ended_by_user_id_foreign FOREIGN KEY (ended_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: assignments assignments_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: assignments assignments_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: assignments assignments_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE RESTRICT;


--
-- Name: contextual_permissions contextual_permissions_granted_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_permissions_granted_by_user_id_foreign FOREIGN KEY (granted_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: contextual_permissions contextual_permissions_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_permissions_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: contextual_permissions contextual_permissions_permission_scope_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_permissions_permission_scope_id_foreign FOREIGN KEY (permission_scope_id) REFERENCES public.permission_scopes(id) ON DELETE CASCADE;


--
-- Name: contextual_permissions contextual_permissions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.contextual_permissions
    ADD CONSTRAINT contextual_permissions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: daily_metrics daily_metrics_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.daily_metrics
    ADD CONSTRAINT daily_metrics_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: depot_assignment_history depot_assignment_history_assigned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_assigned_by_foreign FOREIGN KEY (assigned_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: depot_assignment_history depot_assignment_history_depot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_depot_id_foreign FOREIGN KEY (depot_id) REFERENCES public.vehicle_depots(id) ON DELETE SET NULL;


--
-- Name: depot_assignment_history depot_assignment_history_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: depot_assignment_history depot_assignment_history_previous_depot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_previous_depot_id_foreign FOREIGN KEY (previous_depot_id) REFERENCES public.vehicle_depots(id) ON DELETE SET NULL;


--
-- Name: depot_assignment_history depot_assignment_history_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.depot_assignment_history
    ADD CONSTRAINT depot_assignment_history_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: document_categories document_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories
    ADD CONSTRAINT document_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: document_revisions document_revisions_document_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_revisions
    ADD CONSTRAINT document_revisions_document_id_foreign FOREIGN KEY (document_id) REFERENCES public.documents(id) ON DELETE CASCADE;


--
-- Name: document_revisions document_revisions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_revisions
    ADD CONSTRAINT document_revisions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: documentables documentables_document_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documentables
    ADD CONSTRAINT documentables_document_id_foreign FOREIGN KEY (document_id) REFERENCES public.documents(id) ON DELETE CASCADE;


--
-- Name: documents documents_document_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_document_category_id_foreign FOREIGN KEY (document_category_id) REFERENCES public.document_categories(id) ON DELETE CASCADE;


--
-- Name: documents documents_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: documents documents_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: driver_sanction_histories driver_sanction_histories_sanction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanction_histories
    ADD CONSTRAINT driver_sanction_histories_sanction_id_foreign FOREIGN KEY (sanction_id) REFERENCES public.driver_sanctions(id) ON DELETE CASCADE;


--
-- Name: driver_sanction_histories driver_sanction_histories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanction_histories
    ADD CONSTRAINT driver_sanction_histories_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: driver_sanctions driver_sanctions_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanctions
    ADD CONSTRAINT driver_sanctions_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE CASCADE;


--
-- Name: driver_sanctions driver_sanctions_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanctions
    ADD CONSTRAINT driver_sanctions_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: driver_sanctions driver_sanctions_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_sanctions
    ADD CONSTRAINT driver_sanctions_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: driver_statuses driver_statuses_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: drivers drivers_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: drivers drivers_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.driver_statuses(id) ON DELETE SET NULL;


--
-- Name: drivers drivers_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: drivers drivers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: expense_audit_logs expense_audit_logs_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs
    ADD CONSTRAINT expense_audit_logs_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: expense_audit_logs expense_audit_logs_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs
    ADD CONSTRAINT expense_audit_logs_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: expense_audit_logs expense_audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs
    ADD CONSTRAINT expense_audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: expense_audit_logs expense_audit_logs_vehicle_expense_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_audit_logs
    ADD CONSTRAINT expense_audit_logs_vehicle_expense_id_foreign FOREIGN KEY (vehicle_expense_id) REFERENCES public.vehicle_expenses(id) ON DELETE CASCADE;


--
-- Name: expense_budgets expense_budgets_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_budgets
    ADD CONSTRAINT expense_budgets_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: expense_budgets expense_budgets_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_budgets
    ADD CONSTRAINT expense_budgets_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: expense_groups expense_groups_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups
    ADD CONSTRAINT expense_groups_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: expense_groups expense_groups_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups
    ADD CONSTRAINT expense_groups_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: expense_groups expense_groups_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expense_groups
    ADD CONSTRAINT expense_groups_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: expenses expenses_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: expenses expenses_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE SET NULL;


--
-- Name: expenses expenses_expense_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_expense_type_id_foreign FOREIGN KEY (expense_type_id) REFERENCES public.expense_types(id) ON DELETE RESTRICT;


--
-- Name: expenses expenses_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: expenses expenses_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.expenses
    ADD CONSTRAINT expenses_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE SET NULL;


--
-- Name: comprehensive_audit_logs fk_audit_organization; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE public.comprehensive_audit_logs
    ADD CONSTRAINT fk_audit_organization FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: comprehensive_audit_logs fk_audit_user; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE public.comprehensive_audit_logs
    ADD CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: fuel_refills fuel_refills_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_refills
    ADD CONSTRAINT fuel_refills_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE SET NULL;


--
-- Name: fuel_refills fuel_refills_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_refills
    ADD CONSTRAINT fuel_refills_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: fuel_refills fuel_refills_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_refills
    ADD CONSTRAINT fuel_refills_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_alerts idx_maintenance_alerts_ack_by; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts
    ADD CONSTRAINT idx_maintenance_alerts_ack_by FOREIGN KEY (acknowledged_by) REFERENCES public.users(id);


--
-- Name: maintenance_alerts idx_maintenance_alerts_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts
    ADD CONSTRAINT idx_maintenance_alerts_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_alerts idx_maintenance_alerts_schedule; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts
    ADD CONSTRAINT idx_maintenance_alerts_schedule FOREIGN KEY (maintenance_schedule_id) REFERENCES public.maintenance_schedules(id) ON DELETE CASCADE;


--
-- Name: maintenance_alerts idx_maintenance_alerts_vehicle; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts
    ADD CONSTRAINT idx_maintenance_alerts_vehicle FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_documents idx_maintenance_documents_operation; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents
    ADD CONSTRAINT idx_maintenance_documents_operation FOREIGN KEY (maintenance_operation_id) REFERENCES public.maintenance_operations(id) ON DELETE CASCADE;


--
-- Name: maintenance_documents idx_maintenance_documents_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents
    ADD CONSTRAINT idx_maintenance_documents_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_documents idx_maintenance_documents_uploader; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents
    ADD CONSTRAINT idx_maintenance_documents_uploader FOREIGN KEY (uploaded_by) REFERENCES public.users(id);


--
-- Name: maintenance_operations idx_maintenance_operations_created_by; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_created_by FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: maintenance_operations idx_maintenance_operations_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_operations idx_maintenance_operations_provider; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_provider FOREIGN KEY (provider_id) REFERENCES public.maintenance_providers(id) ON DELETE SET NULL;


--
-- Name: maintenance_operations idx_maintenance_operations_schedule; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_schedule FOREIGN KEY (maintenance_schedule_id) REFERENCES public.maintenance_schedules(id) ON DELETE SET NULL;


--
-- Name: maintenance_operations idx_maintenance_operations_type; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_type FOREIGN KEY (maintenance_type_id) REFERENCES public.maintenance_types(id);


--
-- Name: maintenance_operations idx_maintenance_operations_updated_by; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_updated_by FOREIGN KEY (updated_by) REFERENCES public.users(id);


--
-- Name: maintenance_operations idx_maintenance_operations_vehicle; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT idx_maintenance_operations_vehicle FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_providers idx_maintenance_providers_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_providers
    ADD CONSTRAINT idx_maintenance_providers_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_schedules idx_maintenance_schedules_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT idx_maintenance_schedules_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_schedules idx_maintenance_schedules_type; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT idx_maintenance_schedules_type FOREIGN KEY (maintenance_type_id) REFERENCES public.maintenance_types(id);


--
-- Name: maintenance_schedules idx_maintenance_schedules_vehicle; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT idx_maintenance_schedules_vehicle FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_types idx_maintenance_types_org; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types
    ADD CONSTRAINT idx_maintenance_types_org FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: incidents incidents_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: incidents incidents_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE SET NULL;


--
-- Name: incidents incidents_incident_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_incident_status_id_foreign FOREIGN KEY (incident_status_id) REFERENCES public.incident_statuses(id);


--
-- Name: incidents incidents_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: incidents incidents_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.incidents
    ADD CONSTRAINT incidents_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_logs maintenance_logs_maintenance_plan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_maintenance_plan_id_foreign FOREIGN KEY (maintenance_plan_id) REFERENCES public.maintenance_plans(id) ON DELETE SET NULL;


--
-- Name: maintenance_logs maintenance_logs_maintenance_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_maintenance_status_id_foreign FOREIGN KEY (maintenance_status_id) REFERENCES public.maintenance_statuses(id);


--
-- Name: maintenance_logs maintenance_logs_maintenance_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_maintenance_type_id_foreign FOREIGN KEY (maintenance_type_id) REFERENCES public.maintenance_types(id);


--
-- Name: maintenance_logs maintenance_logs_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_logs maintenance_logs_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: maintenance_plans maintenance_plans_maintenance_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_maintenance_type_id_foreign FOREIGN KEY (maintenance_type_id) REFERENCES public.maintenance_types(id) ON DELETE CASCADE;


--
-- Name: maintenance_plans maintenance_plans_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: maintenance_plans maintenance_plans_recurrence_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_recurrence_unit_id_foreign FOREIGN KEY (recurrence_unit_id) REFERENCES public.recurrence_units(id);


--
-- Name: maintenance_plans maintenance_plans_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: organization_metrics organization_metrics_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organization_metrics
    ADD CONSTRAINT organization_metrics_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: organizations organizations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: organizations organizations_parent_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_parent_organization_id_foreign FOREIGN KEY (parent_organization_id) REFERENCES public.organizations(id) ON DELETE SET NULL;


--
-- Name: organizations organizations_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: permissions permissions_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: repair_categories repair_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_categories
    ADD CONSTRAINT repair_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: repair_notifications repair_notifications_repair_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_notifications
    ADD CONSTRAINT repair_notifications_repair_request_id_foreign FOREIGN KEY (repair_request_id) REFERENCES public.repair_requests(id) ON DELETE CASCADE;


--
-- Name: repair_notifications repair_notifications_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_notifications
    ADD CONSTRAINT repair_notifications_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: repair_request_history repair_request_history_repair_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_request_history
    ADD CONSTRAINT repair_request_history_repair_request_id_foreign FOREIGN KEY (repair_request_id) REFERENCES public.repair_requests(id) ON DELETE CASCADE;


--
-- Name: repair_request_history repair_request_history_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_request_history
    ADD CONSTRAINT repair_request_history_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.repair_categories(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE CASCADE;


--
-- Name: repair_requests repair_requests_final_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_final_approved_by_foreign FOREIGN KEY (final_approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_fleet_manager_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_fleet_manager_id_foreign FOREIGN KEY (fleet_manager_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_maintenance_operation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_maintenance_operation_id_foreign FOREIGN KEY (maintenance_operation_id) REFERENCES public.maintenance_operations(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_manager_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_manager_id_foreign FOREIGN KEY (manager_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: repair_requests repair_requests_rejected_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_rejected_by_foreign FOREIGN KEY (rejected_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_requested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_requested_by_foreign FOREIGN KEY (requested_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: repair_requests repair_requests_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: repair_requests repair_requests_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.repair_requests
    ADD CONSTRAINT repair_requests_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: subscription_changes subscription_changes_initiated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes
    ADD CONSTRAINT subscription_changes_initiated_by_foreign FOREIGN KEY (initiated_by) REFERENCES public.users(id);


--
-- Name: subscription_changes subscription_changes_new_plan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes
    ADD CONSTRAINT subscription_changes_new_plan_id_foreign FOREIGN KEY (new_plan_id) REFERENCES public.subscription_plans(id);


--
-- Name: subscription_changes subscription_changes_old_plan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes
    ADD CONSTRAINT subscription_changes_old_plan_id_foreign FOREIGN KEY (old_plan_id) REFERENCES public.subscription_plans(id);


--
-- Name: subscription_changes subscription_changes_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.subscription_changes
    ADD CONSTRAINT subscription_changes_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: supervisor_driver_assignments supervisor_driver_assignments_assigned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments
    ADD CONSTRAINT supervisor_driver_assignments_assigned_by_foreign FOREIGN KEY (assigned_by) REFERENCES public.users(id);


--
-- Name: supervisor_driver_assignments supervisor_driver_assignments_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments
    ADD CONSTRAINT supervisor_driver_assignments_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE CASCADE;


--
-- Name: supervisor_driver_assignments supervisor_driver_assignments_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supervisor_driver_assignments
    ADD CONSTRAINT supervisor_driver_assignments_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: supplier_categories supplier_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories
    ADD CONSTRAINT supplier_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: supplier_ratings supplier_ratings_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: supplier_ratings supplier_ratings_rated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_rated_by_foreign FOREIGN KEY (rated_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: supplier_ratings supplier_ratings_repair_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_repair_request_id_foreign FOREIGN KEY (repair_request_id) REFERENCES public.repair_requests(id) ON DELETE SET NULL;


--
-- Name: supplier_ratings supplier_ratings_supplier_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_ratings
    ADD CONSTRAINT supplier_ratings_supplier_id_foreign FOREIGN KEY (supplier_id) REFERENCES public.suppliers(id) ON DELETE CASCADE;


--
-- Name: suppliers suppliers_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: user_organizations user_organizations_granted_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT user_organizations_granted_by_user_id_foreign FOREIGN KEY (granted_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_organizations user_organizations_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT user_organizations_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: user_organizations user_organizations_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_organizations
    ADD CONSTRAINT user_organizations_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_validation_levels user_validation_levels_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_validation_levels
    ADD CONSTRAINT user_validation_levels_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_validation_levels user_validation_levels_validation_level_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_validation_levels
    ADD CONSTRAINT user_validation_levels_validation_level_id_foreign FOREIGN KEY (validation_level_id) REFERENCES public.validation_levels(id) ON DELETE CASCADE;


--
-- Name: user_vehicle_assignments user_vehicle_assignments_assigned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments
    ADD CONSTRAINT user_vehicle_assignments_assigned_by_foreign FOREIGN KEY (assigned_by) REFERENCES public.users(id);


--
-- Name: user_vehicle_assignments user_vehicle_assignments_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments
    ADD CONSTRAINT user_vehicle_assignments_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_vehicle_assignments user_vehicle_assignments_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle_assignments
    ADD CONSTRAINT user_vehicle_assignments_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: user_vehicle user_vehicle_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle
    ADD CONSTRAINT user_vehicle_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_vehicle user_vehicle_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.user_vehicle
    ADD CONSTRAINT user_vehicle_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: users users_manager_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_manager_id_foreign FOREIGN KEY (manager_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: users users_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: users users_supervisor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_categories vehicle_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_categories
    ADD CONSTRAINT vehicle_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicle_depots vehicle_depots_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_depots
    ADD CONSTRAINT vehicle_depots_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicle_expenses vehicle_expenses_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_audited_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_audited_by_foreign FOREIGN KEY (audited_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_expense_group_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_expense_group_id_foreign FOREIGN KEY (expense_group_id) REFERENCES public.expense_groups(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_level1_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_level1_approved_by_foreign FOREIGN KEY (level1_approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_level2_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_level2_approved_by_foreign FOREIGN KEY (level2_approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicle_expenses vehicle_expenses_parent_expense_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_parent_expense_id_foreign FOREIGN KEY (parent_expense_id) REFERENCES public.vehicle_expenses(id) ON DELETE CASCADE;


--
-- Name: vehicle_expenses vehicle_expenses_recorded_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: vehicle_expenses vehicle_expenses_rejected_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_rejected_by_foreign FOREIGN KEY (rejected_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_repair_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_repair_request_id_foreign FOREIGN KEY (repair_request_id) REFERENCES public.repair_requests(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_requester_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_requester_id_foreign FOREIGN KEY (requester_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_supplier_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_supplier_id_foreign FOREIGN KEY (supplier_id) REFERENCES public.suppliers(id) ON DELETE SET NULL;


--
-- Name: vehicle_expenses vehicle_expenses_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_expenses
    ADD CONSTRAINT vehicle_expenses_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: vehicle_handover_details vehicle_handover_details_handover_form_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_details
    ADD CONSTRAINT vehicle_handover_details_handover_form_id_foreign FOREIGN KEY (handover_form_id) REFERENCES public.vehicle_handover_forms(id) ON DELETE CASCADE;


--
-- Name: vehicle_handover_forms vehicle_handover_forms_assignment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms
    ADD CONSTRAINT vehicle_handover_forms_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES public.assignments(id) ON DELETE CASCADE;


--
-- Name: vehicle_handover_forms vehicle_handover_forms_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms
    ADD CONSTRAINT vehicle_handover_forms_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.vehicle_categories(id) ON DELETE SET NULL;


--
-- Name: vehicles vehicles_depot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_depot_id_foreign FOREIGN KEY (depot_id) REFERENCES public.vehicle_depots(id) ON DELETE SET NULL;


--
-- Name: vehicles vehicles_fuel_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_fuel_type_id_foreign FOREIGN KEY (fuel_type_id) REFERENCES public.fuel_types(id) ON DELETE SET NULL;


--
-- Name: vehicles vehicles_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.vehicle_statuses(id) ON DELETE SET NULL;


--
-- Name: vehicles vehicles_transmission_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_transmission_type_id_foreign FOREIGN KEY (transmission_type_id) REFERENCES public.transmission_types(id) ON DELETE SET NULL;


--
-- Name: vehicles vehicles_vehicle_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_vehicle_type_id_foreign FOREIGN KEY (vehicle_type_id) REFERENCES public.vehicle_types(id) ON DELETE SET NULL;


--
-- Name: comprehensive_audit_logs audit_organization_isolation; Type: POLICY; Schema: public; Owner: zenfleet_user
--

CREATE POLICY audit_organization_isolation ON public.comprehensive_audit_logs USING ((organization_id = COALESCE((current_setting('app.current_organization_id'::text, true))::bigint, ( SELECT users.organization_id
   FROM public.users
  WHERE (users.id = (current_setting('app.current_user_id'::text, true))::bigint)))));


--
-- Name: comprehensive_audit_logs; Type: ROW SECURITY; Schema: public; Owner: zenfleet_user
--

ALTER TABLE public.comprehensive_audit_logs ENABLE ROW LEVEL SECURITY;

--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database cluster dump complete
--

