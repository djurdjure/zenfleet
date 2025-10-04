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
-- Name: calculate_vehicle_metrics(bigint, integer); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.calculate_vehicle_metrics(p_vehicle_id bigint, p_days integer DEFAULT 30) RETURNS TABLE(avg_fuel_consumption numeric, total_distance numeric, avg_speed numeric, harsh_events_count integer, engine_hours numeric)
    LANGUAGE plpgsql
    AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    COALESCE(AVG(td.fuel_level), 0)::DECIMAL,
                    COALESCE(MAX(td.odometer) - MIN(td.odometer), 0)::DECIMAL,
                    COALESCE(AVG(td.speed), 0)::DECIMAL,
                    COALESCE(SUM(CASE WHEN td.harsh_acceleration OR td.harsh_braking OR td.harsh_cornering THEN 1 ELSE 0 END), 0)::INTEGER,
                    COALESCE(MAX(td.engine_hours) - MIN(td.engine_hours), 0)::DECIMAL
                FROM telematics_data td
                WHERE td.vehicle_id = p_vehicle_id
                AND td.recorded_at >= NOW() - (p_days || ' days')::INTERVAL;
            END;
            $$;


ALTER FUNCTION public.calculate_vehicle_metrics(p_vehicle_id bigint, p_days integer) OWNER TO zenfleet_user;

--
-- Name: cleanup_old_telematics_data(); Type: FUNCTION; Schema: public; Owner: zenfleet_user
--

CREATE FUNCTION public.cleanup_old_telematics_data() RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
                retention_days INTEGER := 90; -- 3 mois par défaut
                cutoff_date DATE;
                partition_name TEXT;
            BEGIN
                cutoff_date := CURRENT_DATE - (retention_days || ' days')::INTERVAL;

                -- Supprime les partitions trop anciennes
                FOR partition_name IN
                    SELECT schemaname||'.'||tablename
                    FROM pg_tables
                    WHERE tablename LIKE 'telematics_data_%'
                    AND tablename < 'telematics_data_' || to_char(cutoff_date, 'YYYY_MM')
                LOOP
                    EXECUTE 'DROP TABLE IF EXISTS ' || partition_name || ' CASCADE';
                    RAISE NOTICE 'Dropped telematics partition: %', partition_name;
                END LOOP;
            END;
            $$;


ALTER FUNCTION public.cleanup_old_telematics_data() OWNER TO zenfleet_user;

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
    organization_id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    driver_id bigint NOT NULL,
    created_by bigint NOT NULL,
    updated_by bigint,
    start_datetime timestamp(0) without time zone NOT NULL,
    end_datetime timestamp(0) without time zone,
    start_mileage integer NOT NULL,
    end_mileage integer,
    reason character varying(255),
    notes text,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT chk_datetime_coherence CHECK (((end_datetime IS NULL) OR (end_datetime > start_datetime))),
    CONSTRAINT chk_mileage_progression CHECK ((((start_mileage IS NULL) AND (end_mileage IS NULL)) OR ((start_mileage IS NOT NULL) AND (end_mileage IS NULL)) OR ((start_mileage IS NOT NULL) AND (end_mileage IS NOT NULL) AND (end_mileage >= start_mileage)))),
    CONSTRAINT chk_realistic_mileage CHECK ((((start_mileage IS NULL) OR (start_mileage >= 0)) AND ((end_mileage IS NULL) OR (end_mileage >= 0)) AND ((start_mileage IS NULL) OR (start_mileage <= 9999999)) AND ((end_mileage IS NULL) OR (end_mileage <= 9999999))))
);


ALTER TABLE public.assignments OWNER TO zenfleet_user;

--
-- Name: assignment_stats_daily; Type: MATERIALIZED VIEW; Schema: public; Owner: zenfleet_user
--

CREATE MATERIALIZED VIEW public.assignment_stats_daily AS
 SELECT organization_id,
    date(start_datetime) AS assignment_date,
    count(*) AS total_assignments,
    count(*) FILTER (WHERE (end_datetime IS NULL)) AS ongoing_assignments,
    count(DISTINCT vehicle_id) AS vehicles_used,
    count(DISTINCT driver_id) AS drivers_used,
    avg((EXTRACT(epoch FROM (COALESCE((end_datetime)::timestamp with time zone, now()) - (start_datetime)::timestamp with time zone)) / (3600)::numeric)) AS avg_duration_hours
   FROM public.assignments
  WHERE (deleted_at IS NULL)
  GROUP BY organization_id, (date(start_datetime))
  ORDER BY organization_id, (date(start_datetime))
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.assignment_stats_daily OWNER TO zenfleet_user;

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
-- Name: driver_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.driver_statuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    color character varying(20) DEFAULT 'blue'::character varying NOT NULL,
    icon character varying(50),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    can_drive boolean DEFAULT true NOT NULL,
    can_assign boolean DEFAULT true NOT NULL,
    requires_validation boolean DEFAULT false NOT NULL,
    organization_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
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
    organization_id bigint NOT NULL,
    user_id bigint,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255),
    personal_phone character varying(255),
    emergency_contact character varying(255),
    emergency_phone character varying(255),
    driver_license_number character varying(255),
    driver_license_expiry_date date,
    driver_license_category character varying(255),
    date_of_birth date,
    address character varying(255),
    city character varying(255),
    postal_code character varying(255),
    hire_date date,
    status character varying(255) DEFAULT 'active'::character varying,
    notes text,
    photo character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    employee_number character varying(100),
    birth_date date,
    personal_email character varying(255),
    full_address text,
    license_number character varying(100),
    license_category character varying(50),
    license_issue_date date,
    license_authority character varying(255),
    recruitment_date date,
    contract_end_date date,
    blood_type character varying(10),
    emergency_contact_name character varying(255),
    emergency_contact_phone character varying(50),
    status_id bigint
);


ALTER TABLE public.drivers OWNER TO zenfleet_user;

--
-- Name: TABLE drivers; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON TABLE public.drivers IS 'Chauffeurs - Table enterprise avec colonnes étendues';


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
-- Name: fuel_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.fuel_types (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
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
    organization_id bigint
);


ALTER TABLE public.model_has_permissions OWNER TO zenfleet_user;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    organization_id bigint
);


ALTER TABLE public.model_has_roles OWNER TO zenfleet_user;

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
    deleted_at timestamp(0) without time zone,
    slug character varying(255) NOT NULL,
    email character varying(255)
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
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id bigint
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
    rating numeric(3,2) DEFAULT '5'::numeric NOT NULL,
    response_time_hours integer DEFAULT 24 NOT NULL,
    quality_score numeric(3,2) DEFAULT '5'::numeric NOT NULL,
    reliability_score numeric(3,2) DEFAULT '5'::numeric NOT NULL,
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
    CONSTRAINT suppliers_supplier_type_check CHECK (((supplier_type)::text = ANY ((ARRAY['mecanicien'::character varying, 'assureur'::character varying, 'station_service'::character varying, 'pieces_detachees'::character varying, 'peinture_carrosserie'::character varying, 'pneumatiques'::character varying, 'electricite_auto'::character varying, 'controle_technique'::character varying, 'transport_vehicules'::character varying, 'autre'::character varying])::text[]))),
    CONSTRAINT valid_contract_dates CHECK (((contract_start_date IS NULL) OR (contract_end_date IS NULL) OR (contract_start_date <= contract_end_date))),
    CONSTRAINT valid_nif CHECK (((nif IS NULL) OR ((char_length((nif)::text) = 15) AND ((nif)::text ~ '^[0-9]{15}$'::text)))),
    CONSTRAINT valid_rating CHECK (((rating >= (0)::numeric) AND (rating <= (10)::numeric))),
    CONSTRAINT valid_scores CHECK ((((quality_score >= (0)::numeric) AND (quality_score <= (10)::numeric)) AND ((reliability_score >= (0)::numeric) AND (reliability_score <= (10)::numeric)))),
    CONSTRAINT valid_trade_register CHECK (((trade_register IS NULL) OR ((trade_register)::text ~ '^[0-9]{2}/[0-9]{2}-[0-9]{7}$'::text)))
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
    name character varying(255) NOT NULL,
    description character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
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
    phone character varying(255),
    role character varying(255) DEFAULT 'user'::character varying NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL
);


ALTER TABLE public.users OWNER TO zenfleet_user;

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
-- Name: vehicle_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.vehicle_statuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    color_code character varying(255) DEFAULT '#6b7280'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
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
    name character varying(255) NOT NULL,
    description character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
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
    organization_id bigint NOT NULL,
    registration_plate character varying(255) NOT NULL,
    vin character varying(255),
    brand character varying(255),
    model character varying(255),
    color character varying(255),
    vehicle_type_id bigint,
    fuel_type_id bigint,
    transmission_type_id bigint,
    status_id bigint,
    manufacturing_year integer,
    acquisition_date date,
    purchase_price numeric(12,2),
    current_value numeric(12,2),
    initial_mileage integer,
    current_mileage integer,
    engine_displacement_cc integer,
    power_hp integer,
    seats integer,
    notes text,
    photo character varying(255),
    status character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.vehicles OWNER TO zenfleet_user;

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
-- Name: driver_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses ALTER COLUMN id SET DEFAULT nextval('public.driver_statuses_id_seq'::regclass);


--
-- Name: drivers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers ALTER COLUMN id SET DEFAULT nextval('public.drivers_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: fuel_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_types ALTER COLUMN id SET DEFAULT nextval('public.fuel_types_id_seq'::regclass);


--
-- Name: maintenance_alerts id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_alerts ALTER COLUMN id SET DEFAULT nextval('public.maintenance_alerts_id_seq'::regclass);


--
-- Name: maintenance_documents id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_documents ALTER COLUMN id SET DEFAULT nextval('public.maintenance_documents_id_seq'::regclass);


--
-- Name: maintenance_operations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations ALTER COLUMN id SET DEFAULT nextval('public.maintenance_operations_id_seq'::regclass);


--
-- Name: maintenance_providers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_providers ALTER COLUMN id SET DEFAULT nextval('public.maintenance_providers_id_seq'::regclass);


--
-- Name: maintenance_schedules id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_schedules ALTER COLUMN id SET DEFAULT nextval('public.maintenance_schedules_id_seq'::regclass);


--
-- Name: maintenance_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types ALTER COLUMN id SET DEFAULT nextval('public.maintenance_types_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: organizations id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations ALTER COLUMN id SET DEFAULT nextval('public.organizations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: supplier_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories ALTER COLUMN id SET DEFAULT nextval('public.supplier_categories_id_seq'::regclass);


--
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: transmission_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types ALTER COLUMN id SET DEFAULT nextval('public.transmission_types_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


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
-- Name: assignments assignments_driver_no_overlap; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_driver_no_overlap EXCLUDE USING gist (organization_id WITH =, driver_id WITH =, public.assignment_interval(start_datetime, end_datetime) WITH &&) WHERE ((deleted_at IS NULL)) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


--
-- Name: assignments assignments_vehicle_no_overlap; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_vehicle_no_overlap EXCLUDE USING gist (organization_id WITH =, vehicle_id WITH =, public.assignment_interval(start_datetime, end_datetime) WITH &&) WHERE ((deleted_at IS NULL)) DEFERRABLE INITIALLY DEFERRED;


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
-- Name: driver_statuses driver_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_pkey PRIMARY KEY (id);


--
-- Name: driver_statuses driver_statuses_slug_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_slug_unique UNIQUE (slug);


--
-- Name: drivers drivers_driver_license_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_driver_license_number_unique UNIQUE (driver_license_number);


--
-- Name: drivers drivers_employee_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_employee_number_unique UNIQUE (employee_number);


--
-- Name: drivers drivers_license_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_license_number_unique UNIQUE (license_number);


--
-- Name: drivers drivers_personal_email_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_personal_email_unique UNIQUE (personal_email);


--
-- Name: drivers drivers_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_pkey PRIMARY KEY (id);


--
-- Name: assignments ex_driver_period; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT ex_driver_period EXCLUDE USING gist (driver_id WITH =, organization_id WITH =, tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp without time zone), '[)'::text) WITH &&) WHERE (((deleted_at IS NULL) AND (driver_id IS NOT NULL)));


--
-- Name: assignments ex_vehicle_period; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT ex_vehicle_period EXCLUDE USING gist (vehicle_id WITH =, organization_id WITH =, tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp without time zone), '[)'::text) WITH &&) WHERE ((deleted_at IS NULL));


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
-- Name: fuel_types fuel_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.fuel_types
    ADD CONSTRAINT fuel_types_pkey PRIMARY KEY (id);


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
-- Name: maintenance_operations maintenance_operations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_operations
    ADD CONSTRAINT maintenance_operations_pkey PRIMARY KEY (id);


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
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: organizations organizations_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_pkey PRIMARY KEY (id);


--
-- Name: organizations organizations_slug_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_slug_unique UNIQUE (slug);


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
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: supplier_categories supplier_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories
    ADD CONSTRAINT supplier_categories_pkey PRIMARY KEY (id);


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
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vehicle_statuses vehicle_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_statuses
    ADD CONSTRAINT vehicle_statuses_pkey PRIMARY KEY (id);


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
-- Name: algeria_communes_wilaya_code_name_fr_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX algeria_communes_wilaya_code_name_fr_index ON public.algeria_communes USING btree (wilaya_code, name_fr);


--
-- Name: assignment_stats_daily_organization_id_assignment_date_idx; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX assignment_stats_daily_organization_id_assignment_date_idx ON public.assignment_stats_daily USING btree (organization_id, assignment_date);


--
-- Name: assignment_stats_daily_organization_id_assignment_date_idx1; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX assignment_stats_daily_organization_id_assignment_date_idx1 ON public.assignment_stats_daily USING btree (organization_id, assignment_date);


--
-- Name: assignments_driver_id_start_datetime_end_datetime_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX assignments_driver_id_start_datetime_end_datetime_index ON public.assignments USING btree (driver_id, start_datetime, end_datetime);


--
-- Name: assignments_organization_id_status_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX assignments_organization_id_status_deleted_at_index ON public.assignments USING btree (organization_id, status, deleted_at);


--
-- Name: assignments_start_datetime_end_datetime_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX assignments_start_datetime_end_datetime_index ON public.assignments USING btree (start_datetime, end_datetime);


--
-- Name: assignments_vehicle_id_start_datetime_end_datetime_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX assignments_vehicle_id_start_datetime_end_datetime_index ON public.assignments USING btree (vehicle_id, start_datetime, end_datetime);


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
-- Name: drivers_driver_license_number_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX drivers_driver_license_number_deleted_at_index ON public.drivers USING btree (driver_license_number, deleted_at);


--
-- Name: drivers_organization_id_status_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX drivers_organization_id_status_deleted_at_index ON public.drivers USING btree (organization_id, status, deleted_at);


--
-- Name: idx_assignments_active; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_active ON public.assignments USING btree (vehicle_id, driver_id) WHERE ((end_datetime IS NULL) AND (deleted_at IS NULL));


--
-- Name: idx_assignments_composite; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_composite ON public.assignments USING btree (organization_id, vehicle_id, start_datetime, end_datetime) WHERE (deleted_at IS NULL);


--
-- Name: idx_assignments_driver_temporal; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_driver_temporal ON public.assignments USING gist (organization_id, driver_id, public.assignment_interval(start_datetime, end_datetime)) WHERE (deleted_at IS NULL);


--
-- Name: idx_assignments_org_temporal; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_org_temporal ON public.assignments USING btree (organization_id, start_datetime DESC);


--
-- Name: idx_assignments_period_reporting; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_period_reporting ON public.assignments USING btree (start_datetime, end_datetime) WHERE (deleted_at IS NULL);


--
-- Name: idx_assignments_status_computed; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_status_computed ON public.assignments USING btree (organization_id, public.assignment_computed_status(start_datetime, end_datetime)) WHERE (deleted_at IS NULL);


--
-- Name: idx_assignments_time_range; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_time_range ON public.assignments USING btree (organization_id, start_datetime, end_datetime) WHERE (deleted_at IS NULL);


--
-- Name: idx_assignments_vehicle_temporal; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_assignments_vehicle_temporal ON public.assignments USING gist (organization_id, vehicle_id, public.assignment_interval(start_datetime, end_datetime)) WHERE (deleted_at IS NULL);


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
-- Name: idx_users_email_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_email_deleted ON public.users USING btree (email, deleted_at);


--
-- Name: idx_users_org_status_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_org_status_deleted ON public.users USING btree (organization_id, status, deleted_at);


--
-- Name: idx_vehicles_org_plate; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_org_plate ON public.vehicles USING btree (organization_id, registration_plate) WHERE (deleted_at IS NULL);


--
-- Name: idx_vehicles_registration_plate; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_registration_plate ON public.vehicles USING btree (registration_plate);


--
-- Name: idx_vehicles_vin; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_vin ON public.vehicles USING btree (vin) WHERE (vin IS NOT NULL);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_permissions_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_organization_id_index ON public.model_has_permissions USING btree (organization_id);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: model_has_roles_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_organization_id_index ON public.model_has_roles USING btree (organization_id);


--
-- Name: organizations_city_wilaya_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_city_wilaya_index ON public.organizations USING btree (city, wilaya);


--
-- Name: organizations_name_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_name_index ON public.organizations USING btree (name);


--
-- Name: organizations_organization_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_organization_type_index ON public.organizations USING btree (organization_type);


--
-- Name: organizations_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_status_index ON public.organizations USING btree (status);


--
-- Name: permissions_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX permissions_organization_id_index ON public.permissions USING btree (organization_id);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: roles_name_guard_null_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX roles_name_guard_null_organization_unique ON public.roles USING btree (name, guard_name) WHERE (organization_id IS NULL);


--
-- Name: roles_name_guard_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX roles_name_guard_organization_unique ON public.roles USING btree (name, guard_name, organization_id);


--
-- Name: roles_organization_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX roles_organization_id_index ON public.roles USING btree (organization_id);


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
-- Name: vehicles_organization_id_status_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicles_organization_id_status_deleted_at_index ON public.vehicles USING btree (organization_id, status, deleted_at);


--
-- Name: vehicles_registration_plate_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicles_registration_plate_deleted_at_index ON public.vehicles USING btree (registration_plate, deleted_at);


--
-- Name: vehicles_registration_plate_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX vehicles_registration_plate_organization_unique ON public.vehicles USING btree (registration_plate, organization_id);


--
-- Name: vehicles_vin_organization_unique; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE UNIQUE INDEX vehicles_vin_organization_unique ON public.vehicles USING btree (vin, organization_id);


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
-- Name: assignments assignment_stats_refresh; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER assignment_stats_refresh AFTER INSERT OR DELETE OR UPDATE ON public.assignments FOR EACH STATEMENT EXECUTE FUNCTION public.refresh_assignment_stats();


--
-- Name: assignments trg_validate_assignment_business; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER trg_validate_assignment_business BEFORE INSERT OR UPDATE ON public.assignments FOR EACH ROW EXECUTE FUNCTION public.validate_assignment_business_rules();


--
-- Name: assignments trg_validate_assignment_mileage; Type: TRIGGER; Schema: public; Owner: zenfleet_user
--

CREATE TRIGGER trg_validate_assignment_mileage BEFORE INSERT OR UPDATE ON public.assignments FOR EACH ROW EXECUTE FUNCTION public.validate_assignment_mileage();


--
-- Name: algeria_communes algeria_communes_wilaya_code_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_communes
    ADD CONSTRAINT algeria_communes_wilaya_code_foreign FOREIGN KEY (wilaya_code) REFERENCES public.algeria_wilayas(code);


--
-- Name: assignments assignments_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: assignments assignments_driver_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_driver_id_foreign FOREIGN KEY (driver_id) REFERENCES public.drivers(id) ON DELETE CASCADE;


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
    ADD CONSTRAINT assignments_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


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
-- Name: drivers drivers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers
    ADD CONSTRAINT drivers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: model_has_permissions model_has_permissions_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: permissions permissions_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


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
-- Name: roles roles_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: supplier_categories supplier_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.supplier_categories
    ADD CONSTRAINT supplier_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: suppliers suppliers_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: users users_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_fuel_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_fuel_type_id_foreign FOREIGN KEY (fuel_type_id) REFERENCES public.fuel_types(id);


--
-- Name: vehicles vehicles_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.vehicle_statuses(id);


--
-- Name: vehicles vehicles_transmission_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_transmission_type_id_foreign FOREIGN KEY (transmission_type_id) REFERENCES public.transmission_types(id);


--
-- Name: vehicles vehicles_vehicle_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_vehicle_type_id_foreign FOREIGN KEY (vehicle_type_id) REFERENCES public.vehicle_types(id);


--
-- Name: comprehensive_audit_logs audit_organization_isolation; Type: POLICY; Schema: public; Owner: zenfleet_user
--

CREATE POLICY audit_organization_isolation ON public.comprehensive_audit_logs USING ((organization_id = COALESCE((current_setting('app.current_organization_id'::text, true))::bigint, ( SELECT users.organization_id
   FROM public.users
  WHERE (users.id = (current_setting('app.current_user_id'::text, true))::bigint)))));


--
-- Name: comprehensive_audit_logs audit_super_admin_access; Type: POLICY; Schema: public; Owner: zenfleet_user
--

CREATE POLICY audit_super_admin_access ON public.comprehensive_audit_logs USING ((EXISTS ( SELECT 1
   FROM ((public.users u
     JOIN public.model_has_roles mhr ON ((u.id = mhr.model_id)))
     JOIN public.roles r ON ((mhr.role_id = r.id)))
  WHERE ((u.id = (current_setting('app.current_user_id'::text, true))::bigint) AND ((r.name)::text = 'Super Admin'::text) AND ((mhr.model_type)::text = 'App\\Models\\User'::text)))));


--
-- Name: comprehensive_audit_logs; Type: ROW SECURITY; Schema: public; Owner: zenfleet_user
--

ALTER TABLE public.comprehensive_audit_logs ENABLE ROW LEVEL SECURITY;

--
-- PostgreSQL database dump complete
--

