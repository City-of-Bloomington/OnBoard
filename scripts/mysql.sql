-- @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
create table users (
	id int unsigned not null primary key auto_increment,
	username varchar(30) unique,
	password varchar(32),
	authenticationMethod varchar(20) not null default 'LDAP',
	firstname varchar(128),
	lastname varchar(128),
	email varchar(128),
	address varchar(128),
	city varchar(128),
	zipcode varchar(15),
	homePhone varchar(15),
	workPhone varchar(15),
	about text,
	photoPath varchar(150),
	timestamp timestamp on update CURRENT_TIMESTAMP
) engine=InnoDB;

create table roles (
	id int unsigned not null primary key auto_increment,
	name varchar(30) not null unique
) engine=InnoDB;
insert roles values(1,'Administrator');
insert roles values(2,'Clerk');

create table user_roles (
	user_id int unsigned not null,
	role_id int unsigned not null,
	primary key (user_id,role_id),
	foreign key(user_id) references users (id),
	foreign key(role_id) references roles (id)
) engine=InnoDB;

create table committees (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
) engine=InnoDB;

create table appointers (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null unique
) engine=InnoDB;
insert appointers values(1,'Elected');

create table requirements (
	id int unsigned not null primary key auto_increment,
	text varchar(255) not null
) engine=InnoDB;

create table seats (
	id int unsigned not null primary key auto_increment,
	title varchar(128) not null,
	committee_id int unsigned not null,
	appointer_id int unsigned not null default 1,
	foreign key (appointer_id) references appointers(id),
	foreign key (committee_id) references committees(id)
) engine=InnoDB;

create table seat_requirements (
	seat_id int unsigned not null,
	requirement_id int unsigned not null,
	primary key (seat_id,requirement_id),
	foreign key (seat_id) references seats(id),
	foreign key (requirement_id) references requirements(id)
) engine=InnoDB;

create table members (
	id int unsigned not null primary key auto_increment,
	seat_id int unsigned not null,
	user_id int unsigned not null,
	term_start date,
	term_end date,
	foreign key (seat_id) references seats(id),
	foreign key (user_id) references users(id)
) engine=InnoDB;

create table topicTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
) engine=InnoDB;

create table topics (
	id int unsigned not null primary key auto_increment,
	topicType_id int unsigned not null,
	date date not null,
	number varchar(15) not null,
	description text not null,
	synopsis text not null,
	committee_id int unsigned not null,
	foreign key (topicType_id) references topicTypes(id),
	foreign key (committee_id) references committees(id)
) engine=InnoDB;

create table voteTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
) engine=InnoDB;

create table votes (
	id int unsigned not null primary key auto_increment,
	date date not null,
	voteType_id int unsigned not null,
	topic_id int unsigned not null,
	foreign key (voteType_id) references voteTypes(id),
	foreign key (topic_id) references topics(id)
) engine=InnoDB;

create table votingRecords (
	id int unsigned not null primary key auto_increment,
	member_id int unsigned not null,
	vote_id int unsigned not null,
	vote enum('yes','no','abstain','absent') not null,
	foreign key (vote_id)  references votes(id),
	foreign key (member_id) references members(id)
) engine=InnoDB;
