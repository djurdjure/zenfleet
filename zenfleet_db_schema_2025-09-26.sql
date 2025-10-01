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
    deleted_at timestamp(0) without time zone
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
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO zenfleet_user;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
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
    updated_at timestamp(0) without time zone
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
    brand character varying(255) NOT NULL,
    model character varying(255) NOT NULL,
    color character varying(255),
    vehicle_type_id bigint NOT NULL,
    fuel_type_id bigint NOT NULL,
    transmission_type_id bigint NOT NULL,
    status_id bigint NOT NULL,
    manufacturing_year integer,
    acquisition_date date,
    purchase_price numeric(12,2),
    current_value numeric(12,2),
    initial_mileage integer DEFAULT 0 NOT NULL,
    current_mileage integer DEFAULT 0 NOT NULL,
    engine_displacement_cc integer,
    power_hp integer,
    seats integer,
    notes text,
    photo character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
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
-- Name: algeria_communes id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.algeria_communes ALTER COLUMN id SET DEFAULT nextval('public.algeria_communes_id_seq'::regclass);


--
-- Name: assignments id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments ALTER COLUMN id SET DEFAULT nextval('public.assignments_id_seq'::regclass);


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
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


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
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: transmission_types transmission_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types
    ADD CONSTRAINT transmission_types_pkey PRIMARY KEY (id);


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
-- Name: vehicles vehicles_registration_plate_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_registration_plate_unique UNIQUE (registration_plate);


--
-- Name: vehicles vehicles_vin_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_vin_unique UNIQUE (vin);


--
-- Name: algeria_communes_wilaya_code_name_fr_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX algeria_communes_wilaya_code_name_fr_index ON public.algeria_communes USING btree (wilaya_code, name_fr);


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
-- Name: drivers_driver_license_number_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX drivers_driver_license_number_deleted_at_index ON public.drivers USING btree (driver_license_number, deleted_at);


--
-- Name: drivers_organization_id_status_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX drivers_organization_id_status_deleted_at_index ON public.drivers USING btree (organization_id, status, deleted_at);


--
-- Name: idx_users_email_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_email_deleted ON public.users USING btree (email, deleted_at);


--
-- Name: idx_users_org_status_deleted; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_users_org_status_deleted ON public.users USING btree (organization_id, status, deleted_at);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


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
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: vehicles_organization_id_status_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicles_organization_id_status_deleted_at_index ON public.vehicles USING btree (organization_id, status, deleted_at);


--
-- Name: vehicles_registration_plate_deleted_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX vehicles_registration_plate_deleted_at_index ON public.vehicles USING btree (registration_plate, deleted_at);


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
-- PostgreSQL database dump complete
--

