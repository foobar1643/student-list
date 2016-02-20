CREATE TYPE gender AS ENUM ('male', 'female');
CREATE TYPE status AS ENUM ('resident', 'nonresident');

CREATE TABLE students (
    id serial NOT NULL,
    name character varying(45) NOT NULL,
    surname character varying(45) NOT NULL,
    gender gender NOT NULL,
    sgroup character varying(6) NOT NULL,
    email character varying(75) NOT NULL UNIQUE,
    byear integer NOT NULL,
    status status NOT NULL,
    rating integer NOT NULL,
    token character varying(250),
    CONSTRAINT table_pkey PRIMARY KEY (id),
    CONSTRAINT name_check CHECK(name ~ '^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z[:space:]]{1,15}$'),
    CONSTRAINT surname_check CHECK(surname ~ '^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z[:space:]]{1,20}$'),
    CONSTRAINT sgrop_check CHECK(sgroup ~ '^[-А-ЯЁа-яёa-zA-Z0-9]{2,5}$'),
    CONSTRAINT byear_check CHECK ((byear)::numeric BETWEEN 1900 AND 2000),
    CONSTRAINT rating_check CHECK (rating BETWEEN 0 AND 150)
) WITH (
  OIDS=FALSE
);