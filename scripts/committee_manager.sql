CREATE TABLE users (
	id int unsigned auto_increment UNIQUE NOT NULL, 
	username varchar(30) UNIQUE NOT NULL, 
	password varchar(30), 
	authenticationMethod varchar(20) NOT NULL, 
	firstname varchar(30), 
	lastname varchar(30), 
	email varchar(50), 
	address varchar(50), 
	city varchar(30), 
	zipcode int , 
	homePhone varchar(15), 
	workPhone varchar(15), 
	about text, 
	timestamp timestamp  on update current_timestamp, 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE roles (
	id int unsigned auto_increment NOT NULL, 
	role varchar(30) NOT NULL, 
	PRIMARY KEY(id))  engine=innodb;

CREATE TABLE user_roles (
	user_id int unsigned, 
	role_id int unsigned,
	PRIMARY KEY(user_id, role_id),
	FOREIGN KEY(user_id) REFERENCES users (id),
	FOREIGN KEY(role_id) REFERENCES roles (id))  engine=innodb;

CREATE TABLE committees (
	id int unsigned auto_increment UNIQUE NOT NULL, 
	name varchar(50),
	count int, 
	PRIMARY KEY(id))  engine=innodb;

CREATE TABLE seatAppointment (
	id int unsigned auto_increment UNIQUE NOT NULL, 
	name varchar(50) UNIQUE, 
	PRIMARY KEY(id))  engine=innodb;

CREATE TABLE seats (
	id int unsigned auto_increment UNIQUE NOT NULL, 
	committee_id int unsigned, 
	appointment_id int unsigned NOT NULL, 
	title varchar(50) UNIQUE NOT NULL, 
	vacancy int, 
	PRIMARY KEY(id),
	FOREIGN KEY(appointment_id) REFERENCES seatAppointment (id),
	FOREIGN KEY(committee_id) REFERENCES committees (id))  engine=innodb;

CREATE TABLE restrictions (
	id int unsigned auto_increment UNIQUE NOT NULL, 
	restriction varchar(100), 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE seat_restrictions (
	seat_id int unsigned NOT NULL, 
	restriction_id int unsigned,
	PRIMARY KEY(seat_id, restriction_id),
	FOREIGN KEY(seat_id) REFERENCES seats (id),
	FOREIGN KEY(restriction_id) REFERENCES restrictions (id))  engine=innodb;

CREATE TABLE seat_users (
	seat_id int unsigned, 
	user_id int unsigned, 
	term_start varchar(30), 
	term_end varchar(30),
	PRIMARY KEY(seat_id, user_id),
	FOREIGN KEY(seat_id) REFERENCES seats (id),
	FOREIGN KEY(user_id) REFERENCES users (id))  engine=innodb;

CREATE TABLE applications (
	id int unsigned auto_increment, 
	committee_id int unsigned NOT NULL, 
	resumePath varchar(150) UNIQUE, 
	firstname varchar(50) NOT NULL, 
	lastname varchar(50) NOT NULL, 
	email varchar(50) NOT NULL, 
	address varchar(50), 
	city varchar(30), 
	zipcode int, 
	homePhone varchar(15), 
	workPhone varchar(15), 
	resident int, 
	occupation varchar(50), 
	interest text NOT NULL, 
	qualifications text NOT NULL, 
	timestamp timestamp, 
	PRIMARY KEY(id),
	FOREIGN KEY(committee_id) REFERENCES committees (id))  engine=innodb;

CREATE TABLE user_applications (
	user_id int unsigned NOT NULL, 
	application_id int unsigned,
	PRIMARY KEY(user_id, application_id),
	FOREIGN KEY(user_id) REFERENCES users (id),
	FOREIGN KEY(application_id) REFERENCES applications (id))  engine=innodb;

