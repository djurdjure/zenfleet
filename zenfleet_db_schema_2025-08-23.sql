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
-- Name: document_types; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.document_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.document_types OWNER TO zenfleet_user;

--
-- Name: document_types_id_seq; Type: SEQUENCE; Schema: public; Owner: zenfleet_user
--

CREATE SEQUENCE public.document_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.document_types_id_seq OWNER TO zenfleet_user;

--
-- Name: document_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: zenfleet_user
--

ALTER SEQUENCE public.document_types_id_seq OWNED BY public.document_types.id;


--
-- Name: documents; Type: TABLE; Schema: public; Owner: zenfleet_user
--

CREATE TABLE public.documents (
    id bigint NOT NULL,
    organization_id bigint NOT NULL,
    documentable_type character varying(255) NOT NULL,
    documentable_id bigint NOT NULL,
    document_type_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    file_path character varying(512) NOT NULL,
    file_size bigint NOT NULL,
    mime_type character varying(100) NOT NULL,
    issue_date date,
    expiry_date date,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.documents OWNER TO zenfleet_user;

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
    updated_at timestamp(0) without time zone
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
    deleted_at timestamp(0) without time zone
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
-- Name: document_types id; Type: DEFAULT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_types ALTER COLUMN id SET DEFAULT nextval('public.document_types_id_seq'::regclass);


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
-- Name: document_types document_types_name_unique; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_types
    ADD CONSTRAINT document_types_name_unique UNIQUE (name);


--
-- Name: document_types document_types_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.document_types
    ADD CONSTRAINT document_types_pkey PRIMARY KEY (id);


--
-- Name: documents documents_pkey; Type: CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_pkey PRIMARY KEY (id);


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
-- Name: documents_documentable_type_documentable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX documents_documentable_type_documentable_id_index ON public.documents USING btree (documentable_type, documentable_id);


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
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: zenfleet_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


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
-- Name: documents documents_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: documents documents_document_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_document_type_id_foreign FOREIGN KEY (document_type_id) REFERENCES public.document_types(id);


--
-- Name: documents documents_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


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
-- Name: users users_organization_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: zenfleet_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES public.organizations(id) ON DELETE CASCADE;


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

