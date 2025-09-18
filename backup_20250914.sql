--
-- PostgreSQL database dump
--

-- Dumped from database version 15.13
-- Dumped by pg_dump version 15.13

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

SET default_tablespace = '';

SET default_table_access_method = heap;

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
    organization_id bigint
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


ALTER TABLE public.assignments_id_seq OWNER TO zenfleet_user;

--
-- Name: assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.assignments_id_seq OWNED BY public.assignments.id;


--
-- Name: comprehensive_audit_logs; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.comprehensive_audit_logs (
    id bigint NOT NULL,
    audit_uuid uuid NOT NULL,
    organization_id bigint NOT NULL,
    user_id bigint,
    event_category character varying(255) NOT NULL,
    event_type character varying(255) NOT NULL,
    event_action character varying(255) NOT NULL,
    severity_level character varying(255) DEFAULT 'medium'::character varying NOT NULL,
    event_description text NOT NULL,
    event_data json NOT NULL,
    before_state json,
    after_state json,
    resource_type character varying(255),
    resource_id bigint,
    ip_address character varying(255),
    user_agent text,
    session_id character varying(255),
    gdpr_relevant boolean DEFAULT false NOT NULL,
    occurred_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT comprehensive_audit_logs_severity_level_check CHECK (((severity_level)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'critical'::character varying])::text[])))
);


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


ALTER TABLE public.comprehensive_audit_logs_id_seq OWNER TO zenfleet_user;

--
-- Name: comprehensive_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.comprehensive_audit_logs_id_seq OWNED BY public.comprehensive_audit_logs.id;


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
    meta_schema json
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


ALTER TABLE public.document_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: document_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.document_categories_id_seq OWNED BY public.document_categories.id;


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
    extra_metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
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


ALTER TABLE public.documents_id_seq OWNER TO zenfleet_user;

--
-- Name: documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.documents_id_seq OWNED BY public.documents.id;


--
-- Name: driver_statuses; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.driver_statuses (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
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


ALTER TABLE public.driver_statuses_id_seq OWNER TO zenfleet_user;

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
    photo_path character varying(512),
    birth_date date,
    blood_type character varying(10),
    address text,
    personal_phone character varying(50),
    personal_email character varying(255),
    license_number character varying(100),
    license_category character varying(50),
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
    license_expiry_date date,
    organization_id bigint
);


ALTER TABLE public.drivers OWNER TO zenfleet_user;

--
-- Name: drivers_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.drivers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.drivers_id_seq OWNER TO zenfleet_user;

--
-- Name: drivers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.drivers_id_seq OWNED BY public.drivers.id;


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


ALTER TABLE public.expense_types_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.expenses_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.failed_jobs_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.fuel_refills_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.fuel_types_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.granular_permissions_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.incident_statuses_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.incidents_id_seq OWNER TO zenfleet_user;

--
-- Name: incidents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.incidents_id_seq OWNED BY public.incidents.id;


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


ALTER TABLE public.maintenance_logs_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_logs_id_seq OWNED BY public.maintenance_logs.id;


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


ALTER TABLE public.maintenance_plans_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_plans_id_seq OWNED BY public.maintenance_plans.id;


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


ALTER TABLE public.maintenance_statuses_id_seq OWNER TO zenfleet_user;

--
-- Name: maintenance_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.maintenance_statuses_id_seq OWNED BY public.maintenance_statuses.id;


--
-- Name: maintenance_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.maintenance_types (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    description text
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


ALTER TABLE public.maintenance_types_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.migrations_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.organization_metrics_id_seq OWNER TO zenfleet_user;

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
    address text,
    contact_email character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    slug character varying(255),
    legal_name character varying(255),
    brand_name character varying(255),
    registration_number character varying(255),
    tax_id character varying(255),
    primary_email character varying(255),
    billing_email character varying(255),
    support_email character varying(255),
    primary_phone character varying(255),
    mobile_phone character varying(255),
    website character varying(255),
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
    parent_organization_id bigint,
    hierarchy_level integer DEFAULT 0 NOT NULL,
    created_by bigint,
    updated_by bigint,
    onboarding_completed_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    organization_type character varying(255),
    industry character varying(100),
    description text,
    siret character varying(20),
    vat_number character varying(20),
    legal_form character varying(50),
    registration_date date,
    phone character varying(20),
    address_line_2 character varying(255),
    city character varying(100),
    postal_code character varying(20),
    state_province character varying(100),
    country character varying(2),
    date_format character varying(20) DEFAULT 'd/m/Y'::character varying,
    time_format character varying(10) DEFAULT 'H:i'::character varying,
    logo_path character varying(255),
    max_vehicles integer DEFAULT 25 NOT NULL,
    max_drivers integer DEFAULT 25 NOT NULL,
    max_users integer DEFAULT 10 NOT NULL,
    working_days json,
    admin_user_id bigint,
    CONSTRAINT organizations_compliance_status_check CHECK (((compliance_status)::text = ANY ((ARRAY['compliant'::character varying, 'warning'::character varying, 'non_compliant'::character varying, 'under_review'::character varying])::text[]))),
    CONSTRAINT organizations_subscription_plan_check CHECK (((subscription_plan)::text = ANY ((ARRAY['trial'::character varying, 'basic'::character varying, 'professional'::character varying, 'enterprise'::character varying, 'custom'::character varying])::text[])))
);


ALTER TABLE public.organizations OWNER TO zenfleet_user;

--
-- Name: COLUMN organizations.country; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.organizations.country IS 'Code pays ISO 3166-1 alpha-2';


--
-- Name: COLUMN organizations.working_days; Type: COMMENT; Schema: public; Owner: zenfleet_user
--

COMMENT ON COLUMN public.organizations.working_days IS 'Jours ouvrés [1,2,3,4,5]';


--
-- Name: organizations_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.organizations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.organizations_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.permissions_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.recurrence_units_id_seq OWNER TO zenfleet_user;

--
-- Name: recurrence_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.recurrence_units_id_seq OWNED BY public.recurrence_units.id;


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


ALTER TABLE public.roles_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.subscription_changes_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.subscription_plans_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.supervisor_driver_assignments_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.supplier_categories_id_seq OWNER TO zenfleet_user;

--
-- Name: supplier_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.supplier_categories_id_seq OWNED BY public.supplier_categories.id;


--
-- Name: suppliers; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.suppliers (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    contact_name character varying(255),
    phone character varying(255),
    email character varying(255),
    address text,
    supplier_category_id bigint,
    organization_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
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


ALTER TABLE public.suppliers_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.transmission_types_id_seq OWNER TO zenfleet_user;

--
-- Name: transmission_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.transmission_types_id_seq OWNED BY public.transmission_types.id;


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


ALTER TABLE public.user_vehicle_assignments_id_seq OWNER TO zenfleet_user;

--
-- Name: user_vehicle_assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.user_vehicle_assignments_id_seq OWNED BY public.user_vehicle_assignments.id;


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
    first_name character varying(255),
    last_name character varying(255),
    phone character varying(50),
    organization_id bigint,
    deleted_at timestamp(0) without time zone,
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
    CONSTRAINT users_user_status_check CHECK (((user_status)::text = ANY ((ARRAY['active'::character varying, 'inactive'::character varying, 'suspended'::character varying, 'pending'::character varying])::text[])))
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


ALTER TABLE public.users_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.validation_levels_id_seq OWNER TO zenfleet_user;

--
-- Name: validation_levels_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.validation_levels_id_seq OWNED BY public.validation_levels.id;


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


ALTER TABLE public.vehicle_handover_details_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.vehicle_handover_forms_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicle_handover_forms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicle_handover_forms_id_seq OWNED BY public.vehicle_handover_forms.id;


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


ALTER TABLE public.vehicle_statuses_id_seq OWNER TO zenfleet_user;

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


ALTER TABLE public.vehicle_types_id_seq OWNER TO zenfleet_user;

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
    brand character varying(100),
    model character varying(100),
    color character varying(50),
    vehicle_type_id bigint,
    fuel_type_id bigint,
    transmission_type_id bigint,
    status_id bigint,
    manufacturing_year smallint,
    acquisition_date date,
    purchase_price numeric(12,2),
    current_value numeric(12,2),
    initial_mileage bigint DEFAULT '0'::bigint NOT NULL,
    current_mileage bigint DEFAULT '0'::bigint NOT NULL,
    engine_displacement_cc integer,
    power_hp integer,
    seats smallint,
    status_reason text,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    organization_id bigint
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


ALTER TABLE public.vehicles_id_seq OWNER TO zenfleet_user;

--
-- Name: vehicles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.vehicles_id_seq OWNED BY public.vehicles.id;


--
-- Name: assignments id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments ALTER COLUMN id SET DEFAULT nextval('public.assignments_id_seq'::regclass);


--
-- Name: comprehensive_audit_logs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.comprehensive_audit_logs_id_seq'::regclass);


--
-- Name: document_categories id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories ALTER COLUMN id SET DEFAULT nextval('public.document_categories_id_seq'::regclass);


--
-- Name: documents id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents ALTER COLUMN id SET DEFAULT nextval('public.documents_id_seq'::regclass);


--
-- Name: driver_statuses id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses ALTER COLUMN id SET DEFAULT nextval('public.driver_statuses_id_seq'::regclass);


--
-- Name: drivers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.drivers ALTER COLUMN id SET DEFAULT nextval('public.drivers_id_seq'::regclass);


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
-- Name: maintenance_logs id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs ALTER COLUMN id SET DEFAULT nextval('public.maintenance_logs_id_seq'::regclass);


--
-- Name: maintenance_plans id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans ALTER COLUMN id SET DEFAULT nextval('public.maintenance_plans_id_seq'::regclass);


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
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: transmission_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.transmission_types ALTER COLUMN id SET DEFAULT nextval('public.transmission_types_id_seq'::regclass);


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
-- Name: vehicle_handover_details id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_details ALTER COLUMN id SET DEFAULT nextval('public.vehicle_handover_details_id_seq'::regclass);


--
-- Name: vehicle_handover_forms id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.vehicle_handover_forms ALTER COLUMN id SET DEFAULT nextval('public.vehicle_handover_forms_id_seq'::regclass);


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
-- Data for Name: assignments; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.assignments (id, vehicle_id, driver_id, start_datetime, end_datetime, start_mileage, end_mileage, reason, notes, created_by_user_id, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: comprehensive_audit_logs; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.comprehensive_audit_logs (id, audit_uuid, organization_id, user_id, event_category, event_type, event_action, severity_level, event_description, event_data, before_state, after_state, resource_type, resource_id, ip_address, user_agent, session_id, gdpr_relevant, occurred_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: document_categories; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.document_categories (id, organization_id, name, description, is_active, created_at, updated_at, is_default, meta_schema) FROM stdin;
1	\N	Assurance	Document d'assurance du véhicule.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"fournisseur_id\\",\\"label\\":\\"Fournisseur\\",\\"type\\":\\"entity_select\\",\\"entity\\":\\"supplier\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_debut\\",\\"label\\":\\"Date de D\\\\u00e9but\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_fin\\",\\"label\\":\\"Date de Fin\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
2	\N	Assurance Marchandise	Assurance spécifique pour la marchandise transportée.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"fournisseur_id\\",\\"label\\":\\"Fournisseur\\",\\"type\\":\\"entity_select\\",\\"entity\\":\\"supplier\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_debut\\",\\"label\\":\\"Date de D\\\\u00e9but\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_fin\\",\\"label\\":\\"Date de Fin\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
3	\N	Permis de Circuler	Document autorisant la circulation du véhicule.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"fournisseur_id\\",\\"label\\":\\"Fournisseur\\",\\"type\\":\\"entity_select\\",\\"entity\\":\\"supplier\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_debut\\",\\"label\\":\\"Date de D\\\\u00e9but\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_fin\\",\\"label\\":\\"Date de Fin\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
4	\N	Vignette	Vignette fiscale ou environnementale.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"date_debut\\",\\"label\\":\\"Date de D\\\\u00e9but\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_fin\\",\\"label\\":\\"Date de Fin\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
5	\N	Contrôle Technique	Rapport de contrôle technique du véhicule.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"fournisseur_id\\",\\"label\\":\\"Fournisseur\\",\\"type\\":\\"entity_select\\",\\"entity\\":\\"supplier\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_debut\\",\\"label\\":\\"Date de D\\\\u00e9but\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_fin\\",\\"label\\":\\"Date de Fin\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
6	\N	Constat d'Accident	Constat amiable ou rapport d'accident.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"date_accident\\",\\"label\\":\\"Date de l'Accident\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
7	\N	Fiche Remise Véhicule	Document de remise du véhicule à un chauffeur.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"date_remise\\",\\"label\\":\\"Date de Remise\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_reprise_prevue\\",\\"label\\":\\"Date de Reprise Pr\\\\u00e9vue\\",\\"type\\":\\"date\\",\\"required\\":false,\\"visible\\":true,\\"editable\\":true}]}"
8	\N	Fiche Reprise Véhicule	Document de reprise du véhicule d'un chauffeur.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"date_reprise\\",\\"label\\":\\"Date de Reprise\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
9	\N	Permis de Conduire	Permis de conduire du chauffeur.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"categories_permis\\",\\"label\\":\\"Cat\\\\u00e9gories de Permis\\",\\"type\\":\\"multiselect\\",\\"options\\":[\\"A\\",\\"B\\",\\"C\\",\\"D\\",\\"E\\"],\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"restrictions\\",\\"label\\":\\"Restrictions\\",\\"type\\":\\"textarea\\",\\"required\\":false,\\"visible\\":true,\\"editable\\":true}]}"
10	\N	Avertissement	Avertissement disciplinaire ou observation.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"date_avertissement\\",\\"label\\":\\"Date d'Avertissement\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"observation\\",\\"label\\":\\"Observation\\",\\"type\\":\\"textarea\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
11	\N	Facture	Facture de service ou d'achat.	t	2025-08-25 14:39:43	2025-08-25 14:39:43	t	"{\\"fields\\":[{\\"name\\":\\"montant_ht\\",\\"label\\":\\"Montant HT\\",\\"type\\":\\"number\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"numero_facture\\",\\"label\\":\\"Num\\\\u00e9ro de Facture\\",\\"type\\":\\"string\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true},{\\"name\\":\\"date_emission\\",\\"label\\":\\"Date d'\\\\u00c9mission\\",\\"type\\":\\"date\\",\\"required\\":true,\\"visible\\":true,\\"editable\\":true}]}"
\.


--
-- Data for Name: documentables; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.documentables (document_id, documentable_type, documentable_id) FROM stdin;
\.


--
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.documents (id, uuid, organization_id, document_category_id, user_id, file_path, original_filename, mime_type, size_in_bytes, issue_date, expiry_date, description, extra_metadata, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: driver_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.driver_statuses (id, name) FROM stdin;
1	Disponible
2	En congé
3	Suspendu
4	Inactif
5	En mission
6	Ex-employé
\.


--
-- Data for Name: drivers; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.drivers (id, user_id, employee_number, first_name, last_name, photo_path, birth_date, blood_type, address, personal_phone, personal_email, license_number, license_category, license_issue_date, license_authority, recruitment_date, contract_end_date, status_id, emergency_contact_name, emergency_contact_phone, created_at, updated_at, deleted_at, license_expiry_date, organization_id) FROM stdin;
1	2	EMP-12919	Sebastian	Kautzer	\N	1976-04-02	\N	\N	669-446-5786	\N	LN-06055807	\N	\N	\N	2022-10-08	\N	1	\N	\N	2025-08-25 14:39:43	2025-08-25 14:39:43	\N	\N	1
2	3	EMP-97027	Vallie	Nitzsche	\N	1986-03-29	\N	\N	+1 (820) 800-3325	\N	LN-54506645	\N	\N	\N	2021-05-31	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	1
3	5	EMP-16231	Kaden	Wolf	\N	1975-12-06	\N	\N	856.814.2050	\N	LN-82417180	\N	\N	\N	2025-03-02	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	2
4	6	EMP-11535	Filiberto	Emmerich	\N	1976-07-06	\N	\N	+1-360-248-6482	\N	LN-62452286	\N	\N	\N	2025-08-21	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	2
5	7	EMP-15963	Claude	Tillman	\N	1989-01-16	\N	\N	+17753696969	\N	LN-72812932	\N	\N	\N	2022-12-14	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	2
6	8	EMP-73710	Bryana	Wisoky	\N	1985-07-26	\N	\N	561.356.1104	\N	LN-79912310	\N	\N	\N	2020-12-11	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	2
7	9	EMP-46168	Ezekiel	Kreiger	\N	1995-03-21	\N	\N	+1-334-904-1068	\N	LN-74489468	\N	\N	\N	2022-12-02	\N	1	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	\N	2
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
5	Hybride
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
-- Data for Name: maintenance_logs; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_logs (id, vehicle_id, maintenance_plan_id, maintenance_type_id, maintenance_status_id, performed_on_date, performed_at_mileage, cost, details, performed_by, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: maintenance_plans; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_plans (id, vehicle_id, maintenance_type_id, recurrence_value, recurrence_unit_id, next_due_date, next_due_mileage, notes, deleted_at, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- Data for Name: maintenance_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_statuses (id, name) FROM stdin;
1	Planifiée
2	En cours
3	Terminée
4	Annulée
\.


--
-- Data for Name: maintenance_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.maintenance_types (id, name, description) FROM stdin;
1	Vidange moteur	Changement de l'huile moteur uniquement.
2	Vidange moteur complète	Changement de l'huile moteur et du filtre à huile.
3	Vidange boîte de vitesse	Changement de l'huile de la boîte de vitesse (manuelle ou automatique).
4	Courroie de distribution	Vérification ou remplacement du kit de distribution.
5	Courroie d'accessoires	Vérification ou remplacement de la courroie d'accessoires (poly-V).
6	Pneumatiques	Contrôle ou remplacement des pneus (pression, usure, géométrie).
7	Système de freinage	Contrôle ou remplacement des disques, plaquettes, et purge du liquide de frein.
8	Système électrique	Contrôle de la batterie, de l'alternateur et du démarreur.
9	Filtres	Remplacement des filtres à air, à carburant, et d'habitacle (pollen).
10	Nettoyage FAP/DPF	Nettoyage ou régénération du filtre à particules.
11	Système de climatisation	Recharge de gaz, contrôle d'étanchéité, et remplacement du filtre déshydrateur.
12	Contrôle technique	Inspection réglementaire périodique obligatoire.
13	Vignette automobile	Paiement annuel de la taxe de circulation.
14	Permis de circuler	Renouvellement ou mise à jour de la carte grise / permis de circuler.
15	Assurance automobile	Paiement ou renouvellement de la police d'assurance (RC, tous risques, etc.).
16	Assurance marchandises	Paiement ou renouvellement de l'assurance pour les biens transportés.
17	Autorisation de mise en circulation (AMC)	Contrôles spécifiques pour les véhicules de transport de marchandises ou de personnes.
18	Révision générale constructeur	Entretien complet suivant les préconisations du constructeur.
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2025_06_05_134749_add_custom_fields_to_users_table	1
6	2025_06_05_144141_create_permission_tables	1
7	2025_06_05_150327_create_validation_levels_table	1
8	2025_06_05_150533_create_user_validation_levels_table	1
9	2025_06_06_204451_create_vehicle_types_table	1
10	2025_06_06_204514_create_fuel_types_table	1
11	2025_06_06_204531_create_transmission_types_table	1
12	2025_06_06_204549_create_vehicle_statuses_table	1
13	2025_06_06_205007_create_vehicles_table	1
14	2025_06_07_202347_add_soft_deletes_to_vehicles_table	1
15	2025_06_07_231226_create_driver_statuses_table	1
16	2025_06_07_231452_create_drivers_table	1
17	2025_06_09_144736_create_assignments_table	1
18	2025_06_10_113936_add_license_expiry_date_to_drivers_table	1
19	2025_06_10_160701_create_maintenance_types_table	1
20	2025_06_10_160701_create_recurrence_units_table	1
21	2025_06_10_160702_create_maintenance_statuses_table	1
22	2025_06_10_160703_create_maintenance_plans_table	1
23	2025_06_10_160704_create_maintenance_logs_table	1
24	2025_06_17_233747_create_vehicle_handover_forms_table	1
25	2025_06_17_233757_create_vehicle_handover_details_table	1
26	2025_07_07_000048_create_organizations_table	1
27	2025_07_07_000238_add_organization_id_to_tables	1
28	2025_07_07_160317_add_soft_deletes_to_users_table	1
29	2025_07_22_012046_add_strategic_indexes_to_tables	1
30	2025_07_22_014038_create_fleet_management_extended_tables	1
31	2025_08_12_015505_create_user_vehicle_pivot_table	1
32	2025_08_15_014729_create_supplier_categories_table	1
33	2025_08_15_014730_create_suppliers_table	1
34	2025_08_23_214824_create_document_categories_table	1
35	2025_08_23_214900_create_documents_table	1
36	2025_08_24_223300_add_is_default_and_meta_schema_to_document_categories_table	1
37	2025_09_06_101000_create_enhanced_rbac_system	2
38	2025_09_06_212409_add_missing_columns_to_organizations_table	3
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
4	App\\Models\\User	2
4	App\\Models\\User	3
2	App\\Models\\User	4
4	App\\Models\\User	5
4	App\\Models\\User	6
4	App\\Models\\User	7
4	App\\Models\\User	8
4	App\\Models\\User	9
1	App\\Models\\User	1
2	App\\Models\\User	10
4	App\\Models\\User	11
1	App\\Models\\User	34
2	App\\Models\\User	35
3	App\\Models\\User	36
8	App\\Models\\User	37
4	App\\Models\\User	38
\.


--
-- Data for Name: organization_metrics; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.organization_metrics (id, organization_id, metric_date, metric_period, total_users, active_users, total_vehicles, active_vehicles, total_distance_km, fuel_costs, maintenance_costs, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: organizations; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.organizations (id, uuid, name, address, contact_email, status, created_at, updated_at, slug, legal_name, brand_name, registration_number, tax_id, primary_email, billing_email, support_email, primary_phone, mobile_phone, website, headquarters_address, billing_address, compliance_status, status_changed_at, status_reason, subscription_plan, subscription_tier, subscription_starts_at, subscription_expires_at, trial_ends_at, monthly_rate, annual_rate, currency, plan_limits, current_usage, feature_flags, settings, branding, notification_preferences, two_factor_required, ip_restriction_enabled, password_policy_strength, session_timeout_minutes, gdpr_compliant, gdpr_consent_at, last_activity_at, total_users, active_users, total_vehicles, active_vehicles, timezone, country_code, language, latitude, longitude, parent_organization_id, hierarchy_level, created_by, updated_by, onboarding_completed_at, deleted_at, organization_type, industry, description, siret, vat_number, legal_form, registration_date, phone, address_line_2, city, postal_code, state_province, country, date_format, time_format, logo_path, max_vehicles, max_drivers, max_users, working_days, admin_user_id) FROM stdin;
1	2ccd6871-744f-4bd4-8dec-3ed11dea234e	ZENFLEET Platform	\N	\N	active	2025-08-25 14:39:43	2025-09-06 21:27:04	zenfleet-platform	ZENFLEET Platform	\N	\N	\N	contact@zenfleet-platform.zenfleet.app	\N	\N	\N	\N	\N	{"street":null,"city":null,"postal_code":null,"country":"France"}	\N	under_review	\N	\N	professional	\N	2025-08-25 14:39:43	\N	\N	\N	\N	EUR	{"max_users":100,"max_vehicles":500,"max_drivers":200}	{"users":0,"vehicles":0,"drivers":0}	{"advanced_analytics":true,"api_access":true,"supervisor_management":true}	{"timezone":"Europe\\/Paris","currency":"EUR","language":"fr"}	\N	{"email_notifications":true,"push_notifications":true}	f	f	2	480	f	\N	2025-09-06 12:03:08	6	6	0	0	Europe/Paris	\N	fr	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	FR	d/m/Y	H:i	\N	25	25	10	\N	\N
2	09528537-70c7-4f97-b321-57616ad2074c	Client de Démo Inc.	\N	\N	active	2025-08-25 14:39:44	2025-09-06 21:27:04	client-de-demo-inc	Client de Démo Inc.	\N	\N	\N	contact@client-de-demo-inc.zenfleet.app	\N	\N	\N	\N	\N	{"street":null,"city":null,"postal_code":null,"country":"France"}	\N	under_review	\N	\N	professional	\N	2025-08-25 14:39:44	\N	\N	\N	\N	EUR	{"max_users":100,"max_vehicles":500,"max_drivers":200}	{"users":0,"vehicles":0,"drivers":0}	{"advanced_analytics":true,"api_access":true,"supervisor_management":true}	{"timezone":"Europe\\/Paris","currency":"EUR","language":"fr"}	\N	{"email_notifications":true,"push_notifications":true}	f	f	2	480	f	\N	2025-09-06 12:20:24	11	11	0	0	Europe/Paris	\N	fr	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	FR	d/m/Y	H:i	\N	25	25	10	\N	\N
3	90aacd01-949a-4d9a-ab40-5d3203d17464	Difex	\N	\N	inactive	2025-08-25 16:49:18	2025-09-14 15:08:38	difex	Difex	\N	\N	\N	contact@difex.zenfleet.app	\N	\N	\N	\N	\N	{"street":null,"city":null,"postal_code":null,"country":"France"}	\N	under_review	\N	\N	professional	\N	2025-08-25 16:49:18	\N	\N	\N	\N	EUR	{"max_users":100,"max_vehicles":500,"max_drivers":200}	{"users":0,"vehicles":0,"drivers":0}	{"advanced_analytics":true,"api_access":true,"supervisor_management":true}	{"timezone":"Europe\\/Paris","currency":"EUR","language":"fr"}	\N	{"email_notifications":true,"push_notifications":true}	f	f	2	480	f	\N	\N	0	0	0	0	Europe/Paris	\N	fr	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	FR	d/m/Y	H:i	\N	25	25	10	\N	\N
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
1	view organizations	web	2025-08-25 14:39:42	2025-08-25 14:39:42
2	create organizations	web	2025-08-25 14:39:42	2025-08-25 14:39:42
3	edit organizations	web	2025-08-25 14:39:42	2025-08-25 14:39:42
4	delete organizations	web	2025-08-25 14:39:42	2025-08-25 14:39:42
5	manage roles	web	2025-08-25 14:39:42	2025-08-25 14:39:42
6	view users	web	2025-08-25 14:39:42	2025-08-25 14:39:42
7	create users	web	2025-08-25 14:39:42	2025-08-25 14:39:42
8	edit users	web	2025-08-25 14:39:42	2025-08-25 14:39:42
9	delete users	web	2025-08-25 14:39:42	2025-08-25 14:39:42
10	view vehicles	web	2025-08-25 14:39:42	2025-08-25 14:39:42
11	create vehicles	web	2025-08-25 14:39:42	2025-08-25 14:39:42
12	edit vehicles	web	2025-08-25 14:39:43	2025-08-25 14:39:43
13	delete vehicles	web	2025-08-25 14:39:43	2025-08-25 14:39:43
14	restore vehicles	web	2025-08-25 14:39:43	2025-08-25 14:39:43
15	force delete vehicles	web	2025-08-25 14:39:43	2025-08-25 14:39:43
16	view drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
17	create drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
18	edit drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
19	delete drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
20	restore drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
21	force delete drivers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
22	view assignments	web	2025-08-25 14:39:43	2025-08-25 14:39:43
23	create assignments	web	2025-08-25 14:39:43	2025-08-25 14:39:43
24	edit assignments	web	2025-08-25 14:39:43	2025-08-25 14:39:43
25	end assignments	web	2025-08-25 14:39:43	2025-08-25 14:39:43
26	view maintenance	web	2025-08-25 14:39:43	2025-08-25 14:39:43
27	manage maintenance plans	web	2025-08-25 14:39:43	2025-08-25 14:39:43
28	log maintenance	web	2025-08-25 14:39:43	2025-08-25 14:39:43
29	create handovers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
30	view handovers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
31	edit handovers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
32	delete handovers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
33	upload signed handovers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
34	view suppliers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
35	create suppliers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
36	edit suppliers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
37	delete suppliers	web	2025-08-25 14:39:43	2025-08-25 14:39:43
38	view documents	web	2025-08-25 14:39:43	2025-08-25 14:39:43
39	create documents	web	2025-08-25 14:39:43	2025-08-25 14:39:43
40	edit documents	web	2025-08-25 14:39:43	2025-08-25 14:39:43
41	delete documents	web	2025-08-25 14:39:43	2025-08-25 14:39:43
42	manage document_categories	web	2025-08-25 14:39:43	2025-08-25 14:39:43
43	view_dashboard	web	2025-09-06 11:59:27	2025-09-06 11:59:27
44	view_dashboard_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
45	view_dashboard_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
46	view_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
47	view_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
48	view_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
49	create_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
50	create_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
51	create_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
52	edit_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
53	edit_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
54	edit_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
55	delete_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
56	delete_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
57	delete_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
58	assign_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
59	assign_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
60	assign_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
61	track_vehicles	web	2025-09-06 11:59:27	2025-09-06 11:59:27
62	track_vehicles_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
63	track_vehicles_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
64	view_drivers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
65	view_drivers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
66	view_drivers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
67	create_drivers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
68	create_drivers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
69	create_drivers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
70	edit_drivers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
71	edit_drivers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
72	edit_drivers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
73	delete_drivers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
74	delete_drivers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
75	delete_drivers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
76	assign_drivers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
77	assign_drivers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
78	assign_drivers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
79	view_maintenance	web	2025-09-06 11:59:27	2025-09-06 11:59:27
80	view_maintenance_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
81	view_maintenance_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
82	create_maintenance	web	2025-09-06 11:59:27	2025-09-06 11:59:27
83	create_maintenance_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
84	create_maintenance_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
85	edit_maintenance	web	2025-09-06 11:59:27	2025-09-06 11:59:27
86	edit_maintenance_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
87	edit_maintenance_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
88	delete_maintenance	web	2025-09-06 11:59:27	2025-09-06 11:59:27
89	delete_maintenance_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
90	delete_maintenance_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
91	schedule_maintenance	web	2025-09-06 11:59:27	2025-09-06 11:59:27
92	schedule_maintenance_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
93	schedule_maintenance_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
94	view_assignments	web	2025-09-06 11:59:27	2025-09-06 11:59:27
95	view_assignments_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
96	view_assignments_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
97	create_assignments	web	2025-09-06 11:59:27	2025-09-06 11:59:27
98	create_assignments_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
99	create_assignments_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
100	edit_assignments	web	2025-09-06 11:59:27	2025-09-06 11:59:27
101	edit_assignments_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
102	edit_assignments_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
103	delete_assignments	web	2025-09-06 11:59:27	2025-09-06 11:59:27
104	delete_assignments_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
105	delete_assignments_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
106	view_reports	web	2025-09-06 11:59:27	2025-09-06 11:59:27
107	view_reports_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
108	view_reports_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
109	create_reports	web	2025-09-06 11:59:27	2025-09-06 11:59:27
110	create_reports_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
111	create_reports_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
112	export_reports	web	2025-09-06 11:59:27	2025-09-06 11:59:27
113	export_reports_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
114	export_reports_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
115	view_suppliers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
116	view_suppliers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
117	view_suppliers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
118	create_suppliers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
119	create_suppliers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
120	create_suppliers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
121	edit_suppliers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
122	edit_suppliers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
123	edit_suppliers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
124	delete_suppliers	web	2025-09-06 11:59:27	2025-09-06 11:59:27
125	delete_suppliers_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
126	delete_suppliers_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
127	view_documents	web	2025-09-06 11:59:27	2025-09-06 11:59:27
128	view_documents_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
129	view_documents_own	web	2025-09-06 11:59:27	2025-09-06 11:59:27
130	create_documents	web	2025-09-06 11:59:27	2025-09-06 11:59:27
131	create_documents_supervised	web	2025-09-06 11:59:27	2025-09-06 11:59:27
132	create_documents_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
133	edit_documents	web	2025-09-06 11:59:28	2025-09-06 11:59:28
134	edit_documents_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
135	edit_documents_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
136	delete_documents	web	2025-09-06 11:59:28	2025-09-06 11:59:28
137	delete_documents_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
138	delete_documents_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
139	upload_documents	web	2025-09-06 11:59:28	2025-09-06 11:59:28
140	upload_documents_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
141	upload_documents_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
142	view_users	web	2025-09-06 11:59:28	2025-09-06 11:59:28
143	view_users_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
144	view_users_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
145	create_users	web	2025-09-06 11:59:28	2025-09-06 11:59:28
146	create_users_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
147	create_users_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
148	edit_users	web	2025-09-06 11:59:28	2025-09-06 11:59:28
149	edit_users_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
150	edit_users_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
151	delete_users	web	2025-09-06 11:59:28	2025-09-06 11:59:28
152	delete_users_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
153	delete_users_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
154	invite_users	web	2025-09-06 11:59:28	2025-09-06 11:59:28
155	invite_users_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
156	invite_users_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
157	view_organizations	web	2025-09-06 11:59:28	2025-09-06 11:59:28
158	edit_organizations	web	2025-09-06 11:59:28	2025-09-06 11:59:28
159	view_settings	web	2025-09-06 11:59:28	2025-09-06 11:59:28
160	edit_settings	web	2025-09-06 11:59:28	2025-09-06 11:59:28
161	view_basic_analytics	web	2025-09-06 11:59:28	2025-09-06 11:59:28
162	view_basic_analytics_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
163	view_basic_analytics_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
164	view_advanced_analytics	web	2025-09-06 11:59:28	2025-09-06 11:59:28
165	view_advanced_analytics_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
166	view_advanced_analytics_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
167	export_analytics	web	2025-09-06 11:59:28	2025-09-06 11:59:28
168	export_analytics_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
169	export_analytics_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
170	view_audit	web	2025-09-06 11:59:28	2025-09-06 11:59:28
171	view_audit_supervised	web	2025-09-06 11:59:28	2025-09-06 11:59:28
172	view_audit_own	web	2025-09-06 11:59:28	2025-09-06 11:59:28
173	view_trips	web	2025-09-06 12:03:06	2025-09-06 12:03:06
174	view_trips_supervised	web	2025-09-06 12:03:06	2025-09-06 12:03:06
175	view_trips_own	web	2025-09-06 12:03:06	2025-09-06 12:03:06
176	create_trips	web	2025-09-06 12:03:06	2025-09-06 12:03:06
177	create_trips_supervised	web	2025-09-06 12:03:06	2025-09-06 12:03:06
178	create_trips_own	web	2025-09-06 12:03:06	2025-09-06 12:03:06
179	edit_trips	web	2025-09-06 12:03:06	2025-09-06 12:03:06
180	edit_trips_supervised	web	2025-09-06 12:03:06	2025-09-06 12:03:06
181	edit_trips_own	web	2025-09-06 12:03:06	2025-09-06 12:03:06
182	delete_trips	web	2025-09-06 12:03:06	2025-09-06 12:03:06
183	delete_trips_supervised	web	2025-09-06 12:03:06	2025-09-06 12:03:06
184	delete_trips_own	web	2025-09-06 12:03:06	2025-09-06 12:03:06
185	view_analytics	web	2025-09-06 12:03:06	2025-09-06 12:03:06
186	view_analytics_supervised	web	2025-09-06 12:03:06	2025-09-06 12:03:06
187	view_analytics_own	web	2025-09-06 12:03:06	2025-09-06 12:03:06
188	view_api	web	2025-09-06 12:03:06	2025-09-06 12:03:06
189	manage_api	web	2025-09-06 12:03:06	2025-09-06 12:03:06
190	view_billing	web	2025-09-06 12:03:06	2025-09-06 12:03:06
191	manage_billing	web	2025-09-06 12:03:06	2025-09-06 12:03:06
192	manage system	web	2025-09-06 12:20:22	2025-09-06 12:20:22
193	view all organizations	web	2025-09-06 12:20:22	2025-09-06 12:20:22
194	manage system settings	web	2025-09-06 12:20:22	2025-09-06 12:20:22
195	view system logs	web	2025-09-06 12:20:22	2025-09-06 12:20:22
196	manage system updates	web	2025-09-06 12:20:22	2025-09-06 12:20:22
197	view organization settings	web	2025-09-06 12:20:22	2025-09-06 12:20:22
198	edit organization settings	web	2025-09-06 12:20:22	2025-09-06 12:20:22
199	manage organization billing	web	2025-09-06 12:20:22	2025-09-06 12:20:22
200	view organization analytics	web	2025-09-06 12:20:22	2025-09-06 12:20:22
201	assign vehicles	web	2025-09-06 12:20:22	2025-09-06 12:20:22
202	track vehicles	web	2025-09-06 12:20:22	2025-09-06 12:20:22
203	assign drivers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
204	schedule maintenance	web	2025-09-06 12:20:22	2025-09-06 12:20:22
205	manage supplier contracts	web	2025-09-06 12:20:22	2025-09-06 12:20:22
206	view supplier analytics	web	2025-09-06 12:20:22	2025-09-06 12:20:22
207	view supervised vehicles	web	2025-09-06 12:20:22	2025-09-06 12:20:22
208	track supervised vehicles	web	2025-09-06 12:20:22	2025-09-06 12:20:22
209	view supervised drivers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
210	assign supervised drivers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
211	view supervised assignments	web	2025-09-06 12:20:22	2025-09-06 12:20:22
212	create supervised assignments	web	2025-09-06 12:20:22	2025-09-06 12:20:22
213	edit supervised assignments	web	2025-09-06 12:20:22	2025-09-06 12:20:22
214	view supervised maintenance	web	2025-09-06 12:20:22	2025-09-06 12:20:22
215	create supervised handovers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
216	view supervised reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
217	create supervised reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
218	view own vehicles	web	2025-09-06 12:20:22	2025-09-06 12:20:22
219	view own assignments	web	2025-09-06 12:20:22	2025-09-06 12:20:22
220	update assignment status	web	2025-09-06 12:20:22	2025-09-06 12:20:22
221	view own handovers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
222	sign handovers	web	2025-09-06 12:20:22	2025-09-06 12:20:22
223	view own maintenance	web	2025-09-06 12:20:22	2025-09-06 12:20:22
224	report vehicle issues	web	2025-09-06 12:20:22	2025-09-06 12:20:22
225	log trip data	web	2025-09-06 12:20:22	2025-09-06 12:20:22
226	view basic reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
227	view advanced reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
228	create reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
229	export reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
230	schedule reports	web	2025-09-06 12:20:22	2025-09-06 12:20:22
231	view basic analytics	web	2025-09-06 12:20:22	2025-09-06 12:20:22
232	view advanced analytics	web	2025-09-06 12:20:22	2025-09-06 12:20:22
233	export analytics	web	2025-09-06 12:20:22	2025-09-06 12:20:22
234	view api settings	web	2025-09-06 12:20:22	2025-09-06 12:20:22
235	manage api keys	web	2025-09-06 12:20:22	2025-09-06 12:20:22
236	view audit logs	web	2025-09-06 12:20:22	2025-09-06 12:20:22
237	export audit logs	web	2025-09-06 12:20:22	2025-09-06 12:20:22
238	manage integrations	web	2025-09-06 12:20:22	2025-09-06 12:20:22
239	view billing details	web	2025-09-06 12:20:22	2025-09-06 12:20:22
240	manage billing settings	web	2025-09-06 12:20:22	2025-09-06 12:20:22
241	view system dashboard	web	2025-09-06 12:34:34	2025-09-06 12:34:34
242	manage global settings	web	2025-09-06 12:34:34	2025-09-06 12:34:34
243	create global admins	web	2025-09-06 12:34:34	2025-09-06 12:34:34
244	manage global roles	web	2025-09-06 12:34:34	2025-09-06 12:34:34
245	view all users	web	2025-09-06 12:34:34	2025-09-06 12:34:34
246	suspend organizations	web	2025-09-06 12:34:34	2025-09-06 12:34:34
247	activate organizations	web	2025-09-06 12:34:34	2025-09-06 12:34:34
248	view reports	web	2025-09-06 12:34:34	2025-09-06 12:34:34
249	view analytics	web	2025-09-06 12:34:34	2025-09-06 12:34:34
250	view system metrics	web	2025-09-06 21:19:57	2025-09-06 21:19:57
251	manage system backups	web	2025-09-06 21:19:57	2025-09-06 21:19:57
252	manage organization settings	web	2025-09-06 21:19:57	2025-09-06 21:19:57
253	export organization data	web	2025-09-06 21:19:57	2025-09-06 21:19:57
254	restore users	web	2025-09-06 21:19:57	2025-09-06 21:19:57
255	assign user roles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
256	view user profiles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
257	view roles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
258	edit roles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
259	assign permissions	web	2025-09-06 21:19:57	2025-09-06 21:19:57
260	import vehicles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
261	export vehicles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
262	view vehicle history	web	2025-09-06 21:19:57	2025-09-06 21:19:57
263	manage vehicle status	web	2025-09-06 21:19:57	2025-09-06 21:19:57
264	view driver profiles	web	2025-09-06 21:19:57	2025-09-06 21:19:57
265	manage driver licenses	web	2025-09-06 21:19:57	2025-09-06 21:19:57
266	import drivers	web	2025-09-06 21:19:57	2025-09-06 21:19:57
267	export drivers	web	2025-09-06 21:19:57	2025-09-06 21:19:57
268	view driver history	web	2025-09-06 21:19:57	2025-09-06 21:19:57
269	extend assignments	web	2025-09-06 21:19:57	2025-09-06 21:19:57
270	view assignment history	web	2025-09-06 21:19:57	2025-09-06 21:19:57
271	manage assignment status	web	2025-09-06 21:19:57	2025-09-06 21:19:57
272	view maintenance history	web	2025-09-06 21:19:57	2025-09-06 21:19:57
273	manage maintenance alerts	web	2025-09-06 21:19:57	2025-09-06 21:19:57
274	approve maintenance	web	2025-09-06 21:19:57	2025-09-06 21:19:57
275	view maintenance costs	web	2025-09-06 21:19:57	2025-09-06 21:19:57
276	download handovers	web	2025-09-06 21:19:57	2025-09-06 21:19:57
277	approve handovers	web	2025-09-06 21:19:57	2025-09-06 21:19:57
278	view supplier performance	web	2025-09-06 21:19:57	2025-09-06 21:19:57
279	manage document categories	web	2025-09-06 21:19:57	2025-09-06 21:19:57
280	upload documents	web	2025-09-06 21:19:57	2025-09-06 21:19:57
281	download documents	web	2025-09-06 21:19:57	2025-09-06 21:19:57
282	archive documents	web	2025-09-06 21:19:57	2025-09-06 21:19:57
283	edit reports	web	2025-09-06 21:19:57	2025-09-06 21:19:57
284	delete reports	web	2025-09-06 21:19:57	2025-09-06 21:19:57
285	manage dashboards	web	2025-09-06 21:19:57	2025-09-06 21:19:57
286	manage security settings	web	2025-09-06 21:19:57	2025-09-06 21:19:57
287	view login attempts	web	2025-09-06 21:19:57	2025-09-06 21:19:57
288	manage user sessions	web	2025-09-06 21:19:57	2025-09-06 21:19:57
289	view system security	web	2025-09-06 21:19:57	2025-09-06 21:19:57
290	view api logs	web	2025-09-06 21:19:57	2025-09-06 21:19:57
291	manage webhooks	web	2025-09-06 21:19:57	2025-09-06 21:19:57
292	test integrations	web	2025-09-06 21:19:57	2025-09-06 21:19:57
293	view costs	web	2025-09-06 21:19:57	2025-09-06 21:19:57
294	manage budgets	web	2025-09-06 21:19:57	2025-09-06 21:19:57
295	view financial reports	web	2025-09-06 21:19:57	2025-09-06 21:19:57
296	export financial data	web	2025-09-06 21:19:57	2025-09-06 21:19:57
297	manage invoicing	web	2025-09-06 21:19:57	2025-09-06 21:19:57
298	manage settings	web	2025-09-06 21:19:57	2025-09-06 21:19:57
299	view system configuration	web	2025-09-06 21:19:57	2025-09-06 21:19:57
300	manage notifications	web	2025-09-06 21:19:57	2025-09-06 21:19:57
301	configure alerts	web	2025-09-06 21:19:57	2025-09-06 21:19:57
302	manage email templates	web	2025-09-06 21:19:57	2025-09-06 21:19:57
303	view own profile	web	2025-09-06 21:19:57	2025-09-06 21:19:57
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
1	Jours
2	Mois
3	Kilomètres
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
1	4
2	4
3	4
4	4
5	4
6	4
7	4
8	4
9	4
10	4
11	4
12	4
13	4
14	4
15	4
16	4
17	4
18	4
19	4
20	4
21	4
22	4
23	4
24	4
218	4
219	4
220	4
221	4
222	4
223	4
224	4
225	4
303	4
1	1
2	1
3	1
4	1
5	1
6	1
7	1
8	1
9	1
10	1
11	1
12	1
13	1
14	1
15	1
16	1
17	1
18	1
19	1
20	1
21	1
22	1
23	1
24	1
25	1
26	1
27	1
28	1
29	1
30	1
31	1
32	1
33	1
34	1
35	1
36	1
37	1
38	1
39	1
40	1
41	1
42	1
43	1
44	1
45	1
46	1
47	1
48	1
49	1
50	1
51	1
52	1
53	1
54	1
55	1
56	1
57	1
58	1
59	1
60	1
61	1
62	1
63	1
64	1
65	1
66	1
67	1
68	1
69	1
70	1
71	1
72	1
73	1
74	1
75	1
76	1
77	1
78	1
79	1
80	1
81	1
82	1
83	1
84	1
85	1
86	1
87	1
88	1
89	1
90	1
91	1
92	1
93	1
94	1
95	1
96	1
97	1
98	1
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
222	1
223	1
224	1
225	1
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
259	1
260	1
261	1
262	1
263	1
264	1
265	1
266	1
267	1
268	1
269	1
270	1
271	1
272	1
273	1
274	1
275	1
276	1
277	1
278	1
279	1
280	1
281	1
282	1
283	1
284	1
285	1
286	1
287	1
288	1
289	1
290	1
291	1
292	1
293	1
294	1
295	1
296	1
297	1
298	1
299	1
300	1
301	1
302	1
303	1
1	2
3	2
252	2
199	2
200	2
253	2
6	2
7	2
8	2
9	2
254	2
255	2
256	2
5	2
257	2
258	2
10	2
11	2
12	2
13	2
14	2
201	2
202	2
260	2
261	2
262	2
16	2
17	2
18	2
19	2
20	2
203	2
264	2
265	2
266	2
267	2
22	2
23	2
24	2
25	2
26	2
27	2
28	2
204	2
29	2
30	2
31	2
32	2
33	2
34	2
35	2
36	2
37	2
38	2
39	2
40	2
41	2
279	2
248	2
228	2
229	2
231	2
232	2
233	2
236	2
237	2
10	3
11	3
12	3
201	3
202	3
260	3
261	3
262	3
263	3
16	3
17	3
18	3
203	3
264	3
265	3
266	3
267	3
268	3
22	3
23	3
24	3
25	3
269	3
26	3
27	3
28	3
204	3
272	3
273	3
29	3
30	3
31	3
33	3
277	3
34	3
35	3
36	3
205	3
38	3
39	3
40	3
279	3
248	3
228	3
229	3
231	3
232	3
10	8
262	8
202	8
16	8
264	8
268	8
22	8
270	8
26	8
272	8
30	8
34	8
38	8
248	8
231	8
23	8
24	8
28	8
29	8
31	8
228	8
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	Super Admin	web	2025-08-25 14:39:43	2025-08-25 14:39:43
2	Admin	web	2025-08-25 14:39:43	2025-08-25 14:39:43
3	Gestionnaire Flotte	web	2025-08-25 14:39:43	2025-08-25 14:39:43
4	Chauffeur	web	2025-08-25 14:39:43	2025-08-25 14:39:43
8	supervisor	web	2025-09-06 12:03:06	2025-09-06 12:03:06
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
1	Trial	trial	Essai gratuit 14 jours	trial	0.00	0.00	{"max_users":3,"max_vehicles":10}	["basic_management"]	14	t	t	0	2025-09-06 11:58:36	2025-09-06 11:58:36
2	Professional	professional	Solution complète	professional	299.00	2990.00	{"max_users":100,"max_vehicles":500}	["advanced_management","analytics","api"]	14	t	t	0	2025-09-06 11:58:36	2025-09-06 11:58:36
3	Enterprise	enterprise	Solution enterprise	enterprise	999.00	9990.00	{"max_users":null,"max_vehicles":null}	["everything","white_labeling","sla"]	14	t	t	0	2025-09-06 11:58:36	2025-09-06 11:58:36
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
1	Service GPS	3	2025-08-25 21:54:54	2025-08-25 21:54:54
\.


--
-- Data for Name: suppliers; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.suppliers (id, name, contact_name, phone, email, address, supplier_category_id, organization_id, deleted_at, created_at, updated_at) FROM stdin;
1	Lowe-Dicki	Anya Dickinson	1-717-463-4259	roscoe.bruen@example.org	95253 Keeling Cliffs Suite 942\nPort Mike, OH 43364-8189	\N	1	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
2	Fisher Group	Willow Jacobi	(619) 301-9660	schuster.sienna@example.com	712 Pouros Lights Suite 517\nSouth Ravenburgh, MA 93052-8528	\N	1	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
3	Reilly, Sanford and Koepp	Roman Keebler	(478) 764-2868	kenneth.okeefe@example.net	60301 Paolo Stravenue\nEffertzland, MD 44707	\N	2	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
4	Muller Group	Dr. Colt Schoen DDS	+1-641-247-0694	buckridge.isaias@example.net	679 Freeda Station\nDarrellville, IA 20904-1502	\N	2	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
5	Lockman-Bauch	Tillman Murazik	1-585-347-2990	ullrich.freda@example.net	430 Auer Hill Suite 784\nPort Noble, MS 05898-8267	\N	2	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
6	Bogan Ltd	Adriana Carroll	+1-906-950-2894	amcdermott@example.com	11225 Jenkins Harbors Apt. 685\nSouth Darwinchester, DE 25813	\N	2	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
7	Beahan, Bechtelar and Borer	Oren Ratke	1-928-690-7110	krystal.nienow@example.com	11570 Larkin Roads Suite 860\nPort Uriel, UT 32693	\N	2	\N	2025-08-25 14:39:44	2025-08-25 14:39:44
8	DZ LYNX	Mouloud	05 61 61 44 90	mouloud@dzlynx.com	Blida\r\nRue el Houma, BP 9364	1	3	\N	2025-08-25 21:55:43	2025-08-25 21:55:43
\.


--
-- Data for Name: transmission_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.transmission_types (id, name) FROM stdin;
1	Manuelle
2	Automatique
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

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, first_name, last_name, phone, organization_id, deleted_at, supervisor_id, manager_id, is_super_admin, permissions_cache, job_title, department, employee_id, hire_date, birth_date, two_factor_enabled, failed_login_attempts, locked_until, password_changed_at, last_activity_at, last_login_at, last_login_ip, login_count, is_active, user_status, timezone, language, preferences, notification_preferences) FROM stdin;
2	Sebastian Kautzer	carolyn.johns@example.com	2025-08-25 14:39:43	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	j1PFm4UqdV	2025-08-25 14:39:43	2025-08-25 14:39:44	Sebastian	Kautzer	\N	1	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
3	Vallie Nitzsche	kshlerin.erika@example.net	2025-08-25 14:39:43	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	K1XndE2Qap	2025-08-25 14:39:43	2025-08-25 14:39:44	Vallie	Nitzsche	\N	1	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
4	Admin Client	client.admin@exemple.com	2025-08-25 14:39:44	$2y$12$G7tnQJ/J3.mTscI05BePY.FnAbj5YOgD50sS0Q5WzF1IXMFf7oDne	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	Admin	Client	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
5	Kaden Wolf	schaden.larue@example.com	2025-08-25 14:39:44	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	5lo6IdxRx5	2025-08-25 14:39:44	2025-08-25 14:39:44	Kaden	Wolf	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
6	Filiberto Emmerich	dschaden@example.com	2025-08-25 14:39:44	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	Q2ZCUrvx4m	2025-08-25 14:39:44	2025-08-25 14:39:44	Filiberto	Emmerich	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
7	Claude Tillman	heller.ernestina@example.org	2025-08-25 14:39:44	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	DsakBEAIWK	2025-08-25 14:39:44	2025-08-25 14:39:44	Claude	Tillman	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
8	Bryana Wisoky	amani45@example.org	2025-08-25 14:39:44	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	n15Wg7BMuy	2025-08-25 14:39:44	2025-08-25 14:39:44	Bryana	Wisoky	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
9	Ezekiel Kreiger	celine20@example.org	2025-08-25 14:39:44	$2y$12$r8PIUIaXbwsqDrFQk/NtSOs1Cog.tkjMBxI0Ykx.M82OmKXRE7QDq	JmYxy0EB6u	2025-08-25 14:39:44	2025-08-25 14:39:44	Ezekiel	Kreiger	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
1	Super Admin	admin@zenfleet.com	2025-08-25 14:39:43	$2y$12$AjUBHKsCPLuD6M6EmRd3Nu/3t/toPuxT4fdR9V2morPVqMrf2q4Qy	\N	2025-08-25 14:39:43	2025-08-25 16:44:34	Super	Admin	\N	2	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
10	El Hadi Chemli	echemli@difex.dz	\N	$2y$12$Lta4.4E7zbc5nWoICTqAG.C207h2C7tLbRCdjlQMJNWPWNnD.U/J2	\N	2025-08-25 16:49:06	2025-08-25 16:49:34	El Hadi	Chemli	\N	3	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
12		zerrouk@dzlynx.com	\N	$2y$12$U3J9uCfbKU681kn3RnsfJe/pjFtX/EBEJknN9q79OymxwziavA1Oq	\N	2025-08-25 23:38:23	2025-08-27 00:11:51	\N	\N	\N	3	2025-08-27 00:11:51	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
13		m.boubenia327@zen.fleet	\N	$2y$12$vMkvXxrzyX.RlFwpq6O53uG1SNMkXIQ3YrGxLIlhs1FInalOBCAQW	\N	2025-08-27 00:10:59	2025-08-27 00:11:54	\N	\N	\N	3	2025-08-27 00:11:54	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
14		O.Berkani@difex.dz	\N	$2y$12$65fQCg84TesHIZ6FqUMzz.kmqmuXBFt0tQuNwFsYM6wVJDIjUc2uK	\N	2025-08-27 00:10:59	2025-08-27 00:11:57	\N	\N	\N	3	2025-08-27 00:11:57	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
11	Said Marzouki	smerzouki@email.com	\N	$2y$12$bPT4AlN.l73OAtxcXR/o/eJ81RAxaca4pRr3OutLyRdFDVJSywFJi	\N	2025-08-25 21:58:02	2025-08-27 00:12:01	Said	Marzouki	\N	3	2025-08-27 00:12:01	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
20		m.boubenia911@zen.fleet	\N	$2y$12$nvkQsz9L3PRW.Qhrh6pB0eGT3eZ09tqXveQ3Ea1NA7k8Xwibe1HTm	\N	2025-09-01 21:07:18	2025-09-01 21:07:55	\N	\N	\N	3	2025-09-01 21:07:55	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
23		m.boubenia414@zen.fleet	\N	$2y$12$qU1ykrCNAO1FGIAq6qVSauJlTFwrKVRuAQ9Vg/ZsLjEYrL4isDOqK	\N	2025-09-01 21:08:37	2025-09-01 21:10:00	\N	\N	\N	3	2025-09-01 21:10:00	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
26		m.boubenia801@zen.fleet	\N	$2y$12$ZpQzlKtx3C58bglZukp2OueHSrCwzVIkfzi7xOnjeGBFKIo5jJA5u	\N	2025-09-01 21:11:19	2025-09-05 13:44:19	\N	\N	\N	3	2025-09-05 13:44:19	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
29		m.boubenia227@zen.fleet	\N	$2y$12$czc4QjdL.CMtSCpiqRLpVeN0E/5wJsNsRwcnXjCj9ivXD6LbCVEyO	\N	2025-09-05 13:44:44	2025-09-05 13:57:07	\N	\N	\N	3	2025-09-05 13:57:07	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
32		m.boubenia397@zen.fleet	\N	$2y$12$5Xe93Zr9W7MnKI5vJIqWgeib7aNRQfDPZOtdWHmqmqhH22dVqkxTO	\N	2025-09-05 14:23:49	2025-09-05 14:26:47	\N	\N	\N	2	2025-09-05 14:26:47	\N	\N	f	\N	\N	\N	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	pending	Europe/Paris	fr	\N	\N
35	Administrateur	admin@zenfleet.app	2025-09-06 21:21:05	$2y$12$OdpvUcjCezHkHAfE8Z39O.aXBoRT0ksfQxSjrIW/AufCWaBfYkMO.	ELbh4Elq0PmoBXW8tnX874nuZSulfpTDdODi2ZrvrPIUPuzhAOa15MOp0586	2025-09-06 12:03:07	2025-09-06 21:21:05	Admin	Organisation	\N	3	\N	\N	\N	f	\N	Administrateur	Direction	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	active	Europe/Paris	fr	{"theme":"auto","dashboard_layout":"advanced","notifications":true}	{"email":true,"push":true,"sms":false,"desktop":true}
37	Superviseur Équipe	supervisor@zenfleet.app	2025-09-06 21:21:05	$2y$12$rqV1QS7srgUMC9Z1f.OQze2WZDKwT3GytBN.fyRiKNDeXldu4KSHG	yvMgzhLcD4	2025-09-06 12:03:07	2025-09-06 21:21:06	Superviseur	Équipe	\N	3	\N	\N	\N	f	\N	Superviseur	Opérations	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	active	Europe/Paris	fr	{"theme":"auto","dashboard_layout":"advanced","notifications":true}	{"email":true,"push":true,"sms":false,"desktop":true}
38	Chauffeur Professionnel	driver@zenfleet.app	2025-09-06 21:21:06	$2y$12$gYmcC7RkZaJ8Mud304gPLeNYBBA/qOiRZ9ZyRXNi6pfNgeFCihy.a	zItx5Cdh3N	2025-09-06 12:03:08	2025-09-06 21:21:06	Chauffeur	Pro	\N	3	\N	\N	\N	f	\N	Chauffeur Professionnel	Transport	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	active	Europe/Paris	fr	{"theme":"auto","dashboard_layout":"advanced","notifications":true}	{"email":true,"push":true,"sms":false,"desktop":true}
34	Super Administrateur	superadmin@zenfleet.app	2025-09-06 21:21:05	$2y$12$zuWpZekLTiLLhfsX9XNEjuDiDmOPMbZbwGCThTa4NGidQuv.XUzym	az4onwIjISDTZfkxvooU8KlPeJnped49EJ9zTIXzImO7Bp1nANukxvRVX6Al	2025-09-06 12:03:07	2025-09-06 21:21:05	Super	Admin	\N	\N	\N	\N	\N	t	\N	Super Administrateur Système	IT Système	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	active	Europe/Paris	fr	{"theme":"auto","dashboard_layout":"advanced","notifications":true}	{"email":true,"push":true,"sms":false,"desktop":true}
36	Gestionnaire de Flotte	fleet@zenfleet.app	2025-09-06 21:21:05	$2y$12$RA.S9yMPW1oUXnnYj6sqEe5MR4CTBfAgJRw0OSSIpZ2DfklhIKrDG	PMIvhEjVCG	2025-09-06 12:03:07	2025-09-06 21:21:05	Gestionnaire	Flotte	\N	3	\N	\N	\N	f	\N	Gestionnaire de Flotte	Opérations	\N	\N	\N	f	0	\N	\N	\N	\N	\N	0	t	active	Europe/Paris	fr	{"theme":"auto","dashboard_layout":"advanced","notifications":true}	{"email":true,"push":true,"sms":false,"desktop":true}
\.


--
-- Data for Name: validation_levels; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.validation_levels (id, level_number, name, description, created_at, updated_at) FROM stdin;
1	1	Demandeur	Niveau initial de la demande.	2025-08-25 14:39:43	2025-08-25 14:39:43
2	2	Validation Intermédiaire	Premier niveau d'approbation.	2025-08-25 14:39:43	2025-08-25 14:39:43
3	3	Validation Finale	Approbation finale.	2025-08-25 14:39:43	2025-08-25 14:39:43
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
-- Data for Name: vehicle_statuses; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_statuses (id, name) FROM stdin;
1	Parking
2	En maintenance
3	Hors service
4	En mission
5	En attente
\.


--
-- Data for Name: vehicle_types; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicle_types (id, name) FROM stdin;
1	Berline
2	SUV
3	Utilitaire léger
4	Camion
5	Bus
6	Moto
\.


--
-- Data for Name: vehicles; Type: TABLE DATA; Schema: public; Owner: zenfleet_user
--

COPY public.vehicles (id, registration_plate, vin, brand, model, color, vehicle_type_id, fuel_type_id, transmission_type_id, status_id, manufacturing_year, acquisition_date, purchase_price, current_value, initial_mileage, current_mileage, engine_displacement_cc, power_hp, seats, status_reason, notes, created_at, updated_at, deleted_at, organization_id) FROM stdin;
1	TA-595-VU	w3gop5f0719hw55gp	Peugeot	Sandero	gray	1	5	2	1	2016	\N	4149125.70	1522750.71	44595	72789	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	1
2	IT-320-MB	r4wt9t318teoou5fi	Renault	Sandero	green	6	5	2	1	2025	\N	3701035.36	1186358.72	13993	42981	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	1
3	FV-075-ZB	2d1509kifebv7yg5y	Volkswagen	Golf	lime	3	2	2	1	2025	\N	4360878.29	2638476.42	46220	77914	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	1
4	GP-792-LX	flev441725drl5g46	Renault	208	gray	4	4	1	1	2019	\N	3941464.08	1967391.47	17805	54313	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
5	XT-482-HV	rcl9801ikzkls135j	Peugeot	Sandero	fuchsia	3	1	2	1	2021	\N	4149392.26	3019293.93	7205	52274	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
6	TO-641-VA	pcr6767qw3o988bp6	Peugeot	Yaris	blue	1	4	2	1	2020	\N	2207068.80	3126462.59	36057	39244	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
7	SI-276-YC	ur6184d3up9389r24	Toyota	Sandero	silver	3	1	2	1	2022	\N	3148440.49	1342917.43	45944	58040	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
8	WD-643-SN	94c4i640cbsa2k8pk	Dacia	Sandero	blue	1	2	1	1	2025	\N	4204008.14	3497741.56	26625	65513	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
9	FH-863-UW	67b7v7xq97do1d6zm	Dacia	208	silver	3	4	1	1	2021	\N	2182156.15	3867421.71	26340	32372	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
10	GR-049-EO	xd897iu6x0u7iys30	Renault	208	purple	1	4	1	1	2021	\N	4004323.60	1912992.82	34848	37606	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
11	NW-113-FH	z5i0s6428g26g5l62	Toyota	Sandero	olive	6	3	2	1	2018	\N	1752165.08	969989.17	6624	18760	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
12	UE-997-CV	qf4he1cr8w2l906x6	Volkswagen	Clio	lime	1	5	1	1	2017	\N	4694610.66	2187725.71	39781	44839	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
13	NN-565-QY	ck3xntrey6l98ha40	Renault	Golf	teal	1	2	1	1	2017	\N	1273923.85	1888010.15	22026	68237	\N	\N	\N	\N	\N	2025-08-25 14:39:44	2025-08-25 14:39:44	\N	2
14	736638-220-16	\N	TOYOTA	Yaris	blue	1	2	1	1	\N	2023-02-10	\N	\N	12000	12000	\N	\N	5	\N	\N	2025-08-25 17:39:37	2025-08-25 17:39:37	\N	3
15	AB-123-CD	VF1234567890ABCDE	Renault	Clio	Bleu	1	2	1	1	2020	2021-01-15	15000.00	12000.00	10000	10000	1500	90	5	\N	Véhicule de service	2025-08-25 17:41:37	2025-08-25 17:41:37	\N	3
16	763707-212-16		AUDI	S3	Noire	1	2	1	1	2012	2017-03-12	1600000.00	\N	123890	123890	\N	\N	5	\N	Véhicule de service	2025-08-25 17:43:55	2025-08-25 17:43:55	\N	3
21	98749-2023-16	\N	Peuget	5008	Noire	1	2	1	1	2023	2017-03-12	1600000.00	\N	5000	5000	\N	\N	5	\N	Véhicule de service	2025-08-25 17:58:29	2025-08-25 17:58:29	\N	3
22	87637-124-16	\N	Renault	Logan	Blanche	1	1	1	1	2025	2025-05-10	1600000.00	\N	1000	1000	\N	\N	5	\N	Véhicule de service	2025-08-25 17:58:29	2025-08-25 17:58:29	\N	3
23	093876-120-16	\N	Dacia	Sandero	Rouge	1	2	2	1	2012	2021-01-10	1600000.00	\N	27890	27890	\N	\N	5	\N	Véhicule de service	2025-08-25 17:58:29	2025-08-25 17:58:29	\N	3
\.


--
-- Name: assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.assignments_id_seq', 1, false);


--
-- Name: comprehensive_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.comprehensive_audit_logs_id_seq', 1, false);


--
-- Name: document_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.document_categories_id_seq', 11, true);


--
-- Name: documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.documents_id_seq', 1, false);


--
-- Name: driver_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.driver_statuses_id_seq', 6, true);


--
-- Name: drivers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.drivers_id_seq', 31, true);


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

SELECT pg_catalog.setval('public.fuel_types_id_seq', 5, true);


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
-- Name: maintenance_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_logs_id_seq', 1, false);


--
-- Name: maintenance_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_plans_id_seq', 1, false);


--
-- Name: maintenance_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_statuses_id_seq', 4, true);


--
-- Name: maintenance_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.maintenance_types_id_seq', 18, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 38, true);


--
-- Name: organization_metrics_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.organization_metrics_id_seq', 1, false);


--
-- Name: organizations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.organizations_id_seq', 3, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.permissions_id_seq', 303, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: recurrence_units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.recurrence_units_id_seq', 3, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 12, true);


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

SELECT pg_catalog.setval('public.supplier_categories_id_seq', 1, true);


--
-- Name: suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.suppliers_id_seq', 8, true);


--
-- Name: transmission_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.transmission_types_id_seq', 2, true);


--
-- Name: user_vehicle_assignments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.user_vehicle_assignments_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.users_id_seq', 38, true);


--
-- Name: validation_levels_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.validation_levels_id_seq', 3, true);


--
-- Name: vehicle_handover_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_handover_details_id_seq', 1, false);


--
-- Name: vehicle_handover_forms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_handover_forms_id_seq', 1, false);


--
-- Name: vehicle_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_statuses_id_seq', 5, true);


--
-- Name: vehicle_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicle_types_id_seq', 6, true);


--
-- Name: vehicles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: zenfleet_user
--

SELECT pg_catalog.setval('public.vehicles_id_seq', 23, true);


--
-- Name: assignments assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_pkey PRIMARY KEY (id);


--
-- Name: comprehensive_audit_logs comprehensive_audit_logs_audit_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs
    ADD CONSTRAINT comprehensive_audit_logs_audit_uuid_unique UNIQUE (audit_uuid);


--
-- Name: comprehensive_audit_logs comprehensive_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs
    ADD CONSTRAINT comprehensive_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: document_categories document_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories
    ADD CONSTRAINT document_categories_pkey PRIMARY KEY (id);


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
-- Name: driver_statuses driver_statuses_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_name_unique UNIQUE (name);


--
-- Name: driver_statuses driver_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.driver_statuses
    ADD CONSTRAINT driver_statuses_pkey PRIMARY KEY (id);


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
-- Name: maintenance_logs maintenance_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_logs
    ADD CONSTRAINT maintenance_logs_pkey PRIMARY KEY (id);


--
-- Name: maintenance_plans maintenance_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_plans
    ADD CONSTRAINT maintenance_plans_pkey PRIMARY KEY (id);


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
-- Name: maintenance_types maintenance_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.maintenance_types
    ADD CONSTRAINT maintenance_types_name_unique UNIQUE (name);


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
-- Name: organizations organizations_siret_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_siret_unique UNIQUE (siret);


--
-- Name: organizations organizations_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_uuid_unique UNIQUE (uuid);


--
-- Name: organizations organizations_vat_number_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.organizations
    ADD CONSTRAINT organizations_vat_number_unique UNIQUE (vat_number);


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
-- Name: suppliers suppliers_email_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_email_unique UNIQUE (email);


--
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


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
-- Name: comprehensive_audit_logs_event_category_event_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX comprehensive_audit_logs_event_category_event_type_index ON public.comprehensive_audit_logs USING btree (event_category, event_type);


--
-- Name: comprehensive_audit_logs_organization_id_occurred_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX comprehensive_audit_logs_organization_id_occurred_at_index ON public.comprehensive_audit_logs USING btree (organization_id, occurred_at);


--
-- Name: comprehensive_audit_logs_resource_type_resource_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX comprehensive_audit_logs_resource_type_resource_id_index ON public.comprehensive_audit_logs USING btree (resource_type, resource_id);


--
-- Name: documentables_documentable_type_documentable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documentables_documentable_type_documentable_id_index ON public.documentables USING btree (documentable_type, documentable_id);


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
-- Name: idx_drivers_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_drivers_organization ON public.drivers USING btree (organization_id);


--
-- Name: idx_maintenance_logs_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_maintenance_logs_organization ON public.maintenance_logs USING btree (organization_id);


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
-- Name: idx_vehicles_organization; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_organization ON public.vehicles USING btree (organization_id);


--
-- Name: idx_vehicles_status_org; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX idx_vehicles_status_org ON public.vehicles USING btree (status_id, organization_id);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: organization_metrics_metric_date_metric_period_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organization_metrics_metric_date_metric_period_index ON public.organization_metrics USING btree (metric_date, metric_period);


--
-- Name: organizations_city_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_city_index ON public.organizations USING btree (city);


--
-- Name: organizations_country_city_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_country_city_index ON public.organizations USING btree (country, city);


--
-- Name: organizations_country_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_country_index ON public.organizations USING btree (country);


--
-- Name: organizations_last_activity_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_last_activity_at_index ON public.organizations USING btree (last_activity_at);


--
-- Name: organizations_organization_type_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_organization_type_index ON public.organizations USING btree (organization_type);


--
-- Name: organizations_parent_organization_id_hierarchy_level_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_parent_organization_id_hierarchy_level_index ON public.organizations USING btree (parent_organization_id, hierarchy_level);


--
-- Name: organizations_slug_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_slug_index ON public.organizations USING btree (slug);


--
-- Name: organizations_status_subscription_expires_at_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_status_subscription_expires_at_index ON public.organizations USING btree (status, subscription_expires_at);


--
-- Name: organizations_status_subscription_plan_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_status_subscription_plan_index ON public.organizations USING btree (status, subscription_plan);


--
-- Name: organizations_subscription_plan_status_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX organizations_subscription_plan_status_index ON public.organizations USING btree (subscription_plan, status);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


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
-- Name: assignments assignments_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: assignments assignments_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.assignments
    ADD CONSTRAINT assignments_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE RESTRICT;


--
-- Name: comprehensive_audit_logs comprehensive_audit_logs_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs
    ADD CONSTRAINT comprehensive_audit_logs_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: comprehensive_audit_logs comprehensive_audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.comprehensive_audit_logs
    ADD CONSTRAINT comprehensive_audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: document_categories document_categories_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_categories
    ADD CONSTRAINT document_categories_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


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
-- Name: suppliers suppliers_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


--
-- Name: suppliers suppliers_supplier_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_supplier_category_id_foreign FOREIGN KEY (supplier_category_id) REFERENCES public.supplier_categories(id) ON DELETE SET NULL;


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
-- PostgreSQL database dump complete
--

