--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

CREATE TABLE student_list (
    id integer NOT NULL,
    name text NOT NULL,
    surname text NOT NULL,
    gender text NOT NULL,
    sgroup text NOT NULL,
    email text NOT NULL,
    byear text NOT NULL,
    status text NOT NULL,
    rating integer NOT NULL
);

CREATE SEQUENCE student_list_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE student_list_id_seq OWNED BY student_list.id;

CREATE TABLE tokens (
    token text NOT NULL,
    student_id text NOT NULL
);

ALTER TABLE ONLY student_list ALTER COLUMN id SET DEFAULT nextval('student_list_id_seq'::regclass);


ALTER TABLE ONLY student_list
    ADD CONSTRAINT student_list_pkey PRIMARY KEY (id);


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
