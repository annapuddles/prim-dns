DROP TABLE IF EXISTS alias;

CREATE TABLE alias (
	name VARCHAR(255) NOT NULL,
	auth VARCHAR(63) NOT NULL,
	url VARCHAR(1023),
	last_object VARCHAR(63),
	expires DATETIME,
	PRIMARY KEY (name)
);
