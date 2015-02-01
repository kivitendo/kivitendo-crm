--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: auth; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO postgres;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = auth, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: clients; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE clients (
    id integer NOT NULL,
    name text NOT NULL,
    dbhost text NOT NULL,
    dbport integer DEFAULT 5432 NOT NULL,
    dbname text NOT NULL,
    dbuser text NOT NULL,
    dbpasswd text NOT NULL,
    is_default boolean DEFAULT false NOT NULL
);


ALTER TABLE auth.clients OWNER TO postgres;

--
-- Name: clients_groups; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE clients_groups (
    client_id integer NOT NULL,
    group_id integer NOT NULL
);


ALTER TABLE auth.clients_groups OWNER TO postgres;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: auth; Owner: postgres
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth.clients_id_seq OWNER TO postgres;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: postgres
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- Name: clients_users; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE clients_users (
    client_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE auth.clients_users OWNER TO postgres;

--
-- Name: group_id_seq; Type: SEQUENCE; Schema: auth; Owner: postgres
--

CREATE SEQUENCE group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth.group_id_seq OWNER TO postgres;

--
-- Name: group; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE "group" (
    id integer DEFAULT nextval('group_id_seq'::regclass) NOT NULL,
    name text NOT NULL,
    description text
);


ALTER TABLE auth."group" OWNER TO postgres;

--
-- Name: group_rights; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE group_rights (
    group_id integer NOT NULL,
    "right" text NOT NULL,
    granted boolean NOT NULL
);


ALTER TABLE auth.group_rights OWNER TO postgres;

--
-- Name: schema_info; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE schema_info (
    tag text NOT NULL,
    login text,
    itime timestamp without time zone DEFAULT now()
);


ALTER TABLE auth.schema_info OWNER TO postgres;

--
-- Name: session; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE session (
    id text NOT NULL,
    ip_address inet,
    mtime timestamp without time zone,
    api_token text
);


ALTER TABLE auth.session OWNER TO postgres;

--
-- Name: session_content; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE session_content (
    session_id text,
    sess_key text,
    sess_value text,
    auto_restore boolean
);


ALTER TABLE auth.session_content OWNER TO postgres;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: auth; Owner: postgres
--

CREATE SEQUENCE user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth.user_id_seq OWNER TO postgres;

--
-- Name: user; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE "user" (
    id integer DEFAULT nextval('user_id_seq'::regclass) NOT NULL,
    login text NOT NULL,
    password text
);


ALTER TABLE auth."user" OWNER TO postgres;

--
-- Name: user_config; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE user_config (
    user_id integer NOT NULL,
    cfg_key text NOT NULL,
    cfg_value text
);


ALTER TABLE auth.user_config OWNER TO postgres;

--
-- Name: user_group; Type: TABLE; Schema: auth; Owner: postgres; Tablespace: 
--

CREATE TABLE user_group (
    user_id integer NOT NULL,
    group_id integer NOT NULL
);


ALTER TABLE auth.user_group OWNER TO postgres;

--
-- Name: id; Type: DEFAULT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- Data for Name: clients; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY clients (id, name, dbhost, dbport, dbname, dbuser, dbpasswd, is_default) FROM stdin;
1	Demo Mandant	localhost	5432	demo-db	postgres	kivitendo	t
\.


--
-- Data for Name: clients_groups; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY clients_groups (client_id, group_id) FROM stdin;
1	1
\.


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: postgres
--

SELECT pg_catalog.setval('clients_id_seq', 1, true);


--
-- Data for Name: clients_users; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY clients_users (client_id, user_id) FROM stdin;
1	1
\.


--
-- Data for Name: group; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY "group" (id, name, description) FROM stdin;
1	Admin	Vollzugriff
\.


--
-- Name: group_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: postgres
--

SELECT pg_catalog.setval('group_id_seq', 1, true);


--
-- Data for Name: group_rights; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY group_rights (group_id, "right", granted) FROM stdin;
1	crm_search	t
1	crm_new	t
1	crm_service	t
1	crm_admin	t
1	crm_adminuser	t
1	crm_adminstatus	t
1	crm_email	t
1	crm_termin	t
1	crm_opportunity	t
1	crm_knowhow	t
1	crm_follow	t
1	crm_notices	t
1	crm_other	t
1	customer_vendor_edit	t
1	customer_vendor_all_edit	t
1	part_service_assembly_edit	t
1	part_service_assembly_details	t
1	project_edit	t
1	requirement_spec_edit	t
1	sales_quotation_edit	t
1	sales_order_edit	t
1	sales_delivery_order_edit	t
1	invoice_edit	t
1	dunning_edit	t
1	sales_all_edit	t
1	edit_prices	t
1	show_ar_transactions	t
1	delivery_plan	t
1	delivery_value_report	t
1	request_quotation_edit	t
1	purchase_order_edit	t
1	purchase_delivery_order_edit	t
1	vendor_invoice_edit	t
1	show_ap_transactions	t
1	warehouse_contents	t
1	warehouse_management	t
1	general_ledger	t
1	datev_export	t
1	cash	t
1	report	t
1	advance_turnover_tax_return	t
1	batch_printing	t
1	config	t
1	admin	t
1	email_bcc	t
1	productivity	t
1	display_admin_link	t
\.


--
-- Data for Name: schema_info; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY schema_info (tag, login, itime) FROM stdin;
add_api_token	admin	2015-01-30 07:42:30.46711
add_batch_printing_to_full_access	admin	2015-01-30 07:42:30.472241
auth_schema_normalization_1	admin	2015-01-30 07:42:30.476317
password_hashing	admin	2015-01-30 07:42:30.483762
remove_menustyle_v4	admin	2015-01-30 07:42:30.487051
remove_menustyle_xml	admin	2015-01-30 07:42:30.489976
session_content_auto_restore	admin	2015-01-30 07:42:30.492953
release_3_0_0	admin	2015-01-30 07:42:30.496035
clients	admin	2015-01-30 07:42:30.500752
delivery_plan_rights	admin	2015-01-30 07:42:30.51793
delivery_process_value	admin	2015-01-30 07:42:30.521172
details_and_report_of_parts	admin	2015-01-30 07:42:30.524478
productivity_rights	admin	2015-01-30 07:42:30.527497
requirement_spec_rights	admin	2015-01-30 07:42:30.530418
rights_for_showing_ar_and_ap_transactions	admin	2015-01-30 07:42:30.53346
clients_webdav	admin	2015-01-30 07:42:30.537262
foreign_key_constraints_on_delete	admin	2015-01-30 07:42:30.539113
\.


--
-- Data for Name: session; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY session (id, ip_address, mtime, api_token) FROM stdin;
f4ec2205795fee9fb07fbf07f62b0631	::1	2015-02-01 15:27:18.901197	d48348338a53523bba5b9392526da974
2a184b5c9889f17b9388a1e92d949a92	::1	2015-02-01 17:32:19.578788	a471da1175159a77894e0880fc920112
\.


--
-- Data for Name: session_content; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY session_content (session_id, sess_key, sess_value, auto_restore) FROM stdin;
2a184b5c9889f17b9388a1e92d949a92	session_auth_status_user	--- 0\n	\N
2a184b5c9889f17b9388a1e92d949a92	client_id	--- 1\n	\N
2a184b5c9889f17b9388a1e92d949a92	login	--- demo\n	\N
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY "user" (id, login, password) FROM stdin;
1	demo	{SHA256S}dc69dc9d82a8c71188a3eb8945f62661cf457f5ccab4fbcaa5d70699910710ee
\.


--
-- Data for Name: user_config; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY user_config (user_id, cfg_key, cfg_value) FROM stdin;
1	fax	
1	email	info@lxcars.de
1	dateformat	dd.mm.yy
1	countrycode	de
1	name	Demo User
1	menustyle	neu
1	mandatory_departments	0
1	phone_password	
1	phone_extension	
1	tel	
1	numberformat	1.000,00
1	vclimit	200
1	stylesheet	lx-office-erp.css
1	signature	
\.


--
-- Data for Name: user_group; Type: TABLE DATA; Schema: auth; Owner: postgres
--

COPY user_group (user_id, group_id) FROM stdin;
1	1
\.


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: postgres
--

SELECT pg_catalog.setval('user_id_seq', 1, true);


--
-- Name: clients_dbhost_dbport_dbname_key; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_dbhost_dbport_dbname_key UNIQUE (dbhost, dbport, dbname);


--
-- Name: clients_groups_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY clients_groups
    ADD CONSTRAINT clients_groups_pkey PRIMARY KEY (client_id, group_id);


--
-- Name: clients_name_key; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_name_key UNIQUE (name);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: clients_users_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY clients_users
    ADD CONSTRAINT clients_users_pkey PRIMARY KEY (client_id, user_id);


--
-- Name: group_name_key; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_name_key UNIQUE (name);


--
-- Name: group_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_pkey PRIMARY KEY (id);


--
-- Name: group_rights_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY group_rights
    ADD CONSTRAINT group_rights_pkey PRIMARY KEY (group_id, "right");


--
-- Name: schema_info_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY schema_info
    ADD CONSTRAINT schema_info_pkey PRIMARY KEY (tag);


--
-- Name: session_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY session
    ADD CONSTRAINT session_pkey PRIMARY KEY (id);


--
-- Name: user_config_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user_config
    ADD CONSTRAINT user_config_pkey PRIMARY KEY (user_id, cfg_key);


--
-- Name: user_group_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user_group
    ADD CONSTRAINT user_group_pkey PRIMARY KEY (user_id, group_id);


--
-- Name: user_login_key; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_login_key UNIQUE (login);


--
-- Name: user_pkey; Type: CONSTRAINT; Schema: auth; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: clients_groups_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY clients_groups
    ADD CONSTRAINT clients_groups_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE;


--
-- Name: clients_groups_group_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY clients_groups
    ADD CONSTRAINT clients_groups_group_id_fkey FOREIGN KEY (group_id) REFERENCES "group"(id) ON DELETE CASCADE;


--
-- Name: clients_users_client_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY clients_users
    ADD CONSTRAINT clients_users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE;


--
-- Name: clients_users_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY clients_users
    ADD CONSTRAINT clients_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE;


--
-- Name: group_rights_group_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY group_rights
    ADD CONSTRAINT group_rights_group_id_fkey FOREIGN KEY (group_id) REFERENCES "group"(id) ON DELETE CASCADE;


--
-- Name: session_content_session_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY session_content
    ADD CONSTRAINT session_content_session_id_fkey FOREIGN KEY (session_id) REFERENCES session(id) ON DELETE CASCADE;


--
-- Name: user_config_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY user_config
    ADD CONSTRAINT user_config_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE;


--
-- Name: user_group_group_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY user_group
    ADD CONSTRAINT user_group_group_id_fkey FOREIGN KEY (group_id) REFERENCES "group"(id) ON DELETE CASCADE;


--
-- Name: user_group_user_id_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: postgres
--

ALTER TABLE ONLY user_group
    ADD CONSTRAINT user_group_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

