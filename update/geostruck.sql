--
-- Name: geodb_changelog; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_changelog (
    id integer NOT NULL,
    datum date NOT NULL,
    beschreibung text NOT NULL,
    autor character varying(50) NOT NULL,
    version character varying(8)
);


--
-- Name: geodb_coordinates; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_coordinates (
    loc_id integer NOT NULL,
    coord_type integer NOT NULL,
    lat double precision,
    lon double precision,
    coord_subtype integer,
    valid_since date,
    date_type_since integer,
    valid_until date NOT NULL,
    date_type_until integer NOT NULL
);


--
-- Name: geodb_floatdata; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_floatdata (
    loc_id integer NOT NULL,
    float_type integer NOT NULL,
    float_val double precision NOT NULL,
    valid_since date,
    date_type_since integer,
    valid_until date NOT NULL,
    date_type_until integer NOT NULL
);


--
-- Name: geodb_hierarchies; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_hierarchies (
    loc_id integer NOT NULL,
    level integer NOT NULL,
    id_lvl1 integer NOT NULL,
    id_lvl2 integer,
    id_lvl3 integer,
    id_lvl4 integer,
    id_lvl5 integer,
    id_lvl6 integer,
    id_lvl7 integer,
    id_lvl8 integer,
    id_lvl9 integer,
    valid_since date,
    date_type_since integer,
    valid_until date NOT NULL,
    date_type_until integer NOT NULL,
    CONSTRAINT geodb_hierarchies_level_check CHECK (((level > 0) AND (level <= 9)))
);


--
-- Name: geodb_intdata; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_intdata (
    loc_id integer NOT NULL,
    int_type integer NOT NULL,
    int_val bigint NOT NULL,
    valid_since date,
    date_type_since integer,
    valid_until date NOT NULL,
    date_type_until integer NOT NULL
);


--
-- Name: geodb_locations; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_locations (
    loc_id integer NOT NULL,
    loc_type integer NOT NULL
);


--
-- Name: geodb_textdata; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_textdata (
    loc_id integer NOT NULL,
    text_type integer NOT NULL,
    text_val character varying(255) NOT NULL,
    text_locale character varying(5),
    is_native_lang integer,
    is_default_name integer,
    valid_since date,
    date_type_since integer,
    valid_until date NOT NULL,
    date_type_until integer NOT NULL
);


--
-- Name: geodb_type_names; Type: TABLE; Schema: public; Owner: lxoffice; Tablespace: 
--

CREATE TABLE geodb_type_names (
    type_id integer NOT NULL,
    type_locale character varying(5) NOT NULL,
    name character varying(255) NOT NULL
);



--
-- Name: geodb_changelog_pkey; Type: CONSTRAINT; Schema: public; Owner: lxoffice; Tablespace: 
--

ALTER TABLE ONLY geodb_changelog
    ADD CONSTRAINT geodb_changelog_pkey PRIMARY KEY (id);


--
-- Name: geodb_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: lxoffice; Tablespace: 
--

ALTER TABLE ONLY geodb_locations
    ADD CONSTRAINT geodb_locations_pkey PRIMARY KEY (loc_id);


--
-- Name: geodb_type_names_type_id_key; Type: CONSTRAINT; Schema: public; Owner: lxoffice; Tablespace: 
--

ALTER TABLE ONLY geodb_type_names
    ADD CONSTRAINT geodb_type_names_type_id_key UNIQUE (type_id, type_locale);



--
-- Name: geodb_coordinates_loc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lxoffice
--

ALTER TABLE ONLY geodb_coordinates
    ADD CONSTRAINT geodb_coordinates_loc_id_fkey FOREIGN KEY (loc_id) REFERENCES geodb_locations(loc_id);


--
-- Name: geodb_floatdata_loc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lxoffice
--

ALTER TABLE ONLY geodb_floatdata
    ADD CONSTRAINT geodb_floatdata_loc_id_fkey FOREIGN KEY (loc_id) REFERENCES geodb_locations(loc_id);


--
-- Name: geodb_hierarchies_loc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lxoffice
--

ALTER TABLE ONLY geodb_hierarchies
    ADD CONSTRAINT geodb_hierarchies_loc_id_fkey FOREIGN KEY (loc_id) REFERENCES geodb_locations(loc_id);


--
-- Name: geodb_intdata_loc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lxoffice
--

ALTER TABLE ONLY geodb_intdata
    ADD CONSTRAINT geodb_intdata_loc_id_fkey FOREIGN KEY (loc_id) REFERENCES geodb_locations(loc_id);


--
-- Name: geodb_textdata_loc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lxoffice
--

ALTER TABLE ONLY geodb_textdata
    ADD CONSTRAINT geodb_textdata_loc_id_fkey FOREIGN KEY (loc_id) REFERENCES geodb_locations(loc_id);

