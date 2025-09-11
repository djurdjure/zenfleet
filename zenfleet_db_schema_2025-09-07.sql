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

