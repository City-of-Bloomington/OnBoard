CREATE TABLE users (
	id int unsigned auto_increment, 
	username varchar(30) UNIQUE NOT NULL, 
	password varchar(30), 
	authenticationMethod varchar(20) NOT NULL, 
	firstname varchar(30), 
	lastname varchar(30), 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE roles (
	id int unsigned auto_increment, 
	role varchar(30) NOT NULL, 
	PRIMARY KEY(id)) engine=innodb;
	
CREATE TABLE commissions (
	id int unsigned auto_increment, 
	name varchar(75), 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE seatCategories (
	id int unsigned auto_increment, 
	category varchar(30), 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE seats (
	id int unsigned auto_increment, 
	commission_id int unsigned, 
	category_id int unsigned, 
	title varchar(30), 
	vacancy int unsigned, 
	PRIMARY KEY(id),
	FOREIGN KEY(commission_id) REFERENCES commissions (id),
	FOREIGN KEY(category_id) REFERENCES seatCategories (id)) engine=innodb;

CREATE TABLE user_roles (
	user_id int unsigned, 
	role_id int unsigned,
	PRIMARY KEY(user_id, role_id),
	FOREIGN KEY(user_id) REFERENCES users (id),
	FOREIGN KEY(role_id) REFERENCES roles (id)) engine=innodb;
	
CREATE TABLE restrictions (
	id int unsigned auto_increment, 
	restriction varchar(100), 
	PRIMARY KEY(id)) engine=innodb;

CREATE TABLE seat_restrictions (
	seat_id int unsigned NOT NULL, 
	restriction_id int unsigned,
	PRIMARY KEY(seat_id, restriction_id),
	FOREIGN KEY(seat_id) REFERENCES seats (id),
	FOREIGN KEY(restriction_id) REFERENCES restrictions (id)) engine=innodb;

CREATE TABLE seat_users (
	seat_id int unsigned, 
	user_id int unsigned, 
	term_start varchar(30), 
	term_end varchar(30),
	PRIMARY KEY(seat_id, user_id),
	FOREIGN KEY(seat_id) REFERENCES seats (id),
	FOREIGN KEY(user_id) REFERENCES users (id)) engine=innodb;


