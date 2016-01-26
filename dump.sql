CREATE TYPE gender AS ENUM ('male', 'female');
CREATE TYPE status AS ENUM ('resident', 'nonresident');

CREATE TABLE students (
    id integer NOT NULL,
    name character varying(45) NOT NULL,
    surname character varying(45) NOT NULL,
    gender gender NOT NULL,
    sgroup character varying(6) NOT NULL,
    email character varying(75) NOT NULL,
    byear integer NOT NULL,
    status status NOT NULL,
    rating integer NOT NULL,
    token character varying(250),
    CONSTRAINT byear_check CHECK ((((byear)::numeric > (1899)::numeric) AND ((byear)::numeric < (2000)::numeric))),
    CONSTRAINT rating_check CHECK (((rating > 0) AND (rating < 151)))
);

CREATE SEQUENCE students_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE ONLY students ALTER COLUMN id SET DEFAULT nextval('students_id_seq'::regclass);

ALTER TABLE ONLY students
    ADD CONSTRAINT students_pkey PRIMARY KEY (id);
