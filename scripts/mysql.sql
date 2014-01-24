-- @copyright 2006-2014 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table races (
	id int unsigned not null primary key auto_increment,
	name varchar(50) not null unique
);
insert races set name='Caucasion';
insert races set name='Hispanic';
insert races set name='African American';
insert races set name='Native American';
insert races set name='Asian';
insert races set name='Other';

create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	email varchar(128) unique,
	address varchar(128),
	city varchar(128),
	zipcode varchar(15),
	about text,
	gender enum('male','female'),
	race_id int unsigned,
	birthdate date,
	privateFields varchar(128),
	username varchar(40) unique,
	password varchar(40),
	authenticationMethod varchar(40),
	role varchar(30),
	foreign key (race_id) references races(id)
);
insert people set id=1,firstname='Administrator',lastname='';

create table committees (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	statutoryName varchar(128),
	statuteReference varchar(128),
	dateFormed date,
	website varchar(128),
	description text
);

create table appointers (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null unique
);
insert appointers values(1,'Elected');

create table requirements (
	id int unsigned not null primary key auto_increment,
	text varchar(255) not null
);

create table seats (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	committee_id int unsigned not null,
	appointer_id int unsigned not null default 1,
	maxCurrentTerms tinyint unsigned not null default 1,
	foreign key (appointer_id) references appointers(id),
	foreign key (committee_id) references committees(id)
);

create table seat_requirements (
	seat_id int unsigned not null,
	requirement_id int unsigned not null,
	primary key (seat_id,requirement_id),
	foreign key (seat_id) references seats(id),
	foreign key (requirement_id) references requirements(id)
);

create table terms (
	id int unsigned not null primary key auto_increment,
	seat_id int unsigned not null,
	person_id int unsigned not null,
	term_start date,
	term_end date,
	foreign key (seat_id) references seats(id),
	foreign key (person_id) references people(id)
);

create table offices (
	id int unsigned not null primary key auto_increment,
	committee_id int unsigned not null,
	person_id int unsigned not null,
	title varchar(128) not null,
	startDate date not null,
	endDate date,
	foreign key (committee_id) references committees(id),
	foreign key (person_id) references people(id)
);

create table topicTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table topics (
	id int unsigned not null primary key auto_increment,
	topicType_id int unsigned not null,
	date date not null,
	number varchar(25) not null,
	description text not null,
	synopsis text not null,
	committee_id int unsigned not null,
	foreign key (topicType_id) references topicTypes(id),
	foreign key (committee_id) references committees(id)
);

create table voteTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	ordering tinyint unsigned not null,
	unique (ordering)
);

create table votes (
	id int unsigned not null primary key auto_increment,
	date date not null,
	voteType_id int unsigned not null,
	topic_id int unsigned not null,
	outcome enum('pass','fail'),
	foreign key (voteType_id) references voteTypes(id),
	foreign key (topic_id) references topics(id)
);

create table votingRecords (
	id int unsigned not null primary key auto_increment,
	term_id int unsigned not null,
	vote_id int unsigned not null,
	position enum('yes','no','abstain','absent') not null default 'absent',
	foreign key (term_id) references terms(id),
	foreign key (vote_id)  references votes(id)
);

create table tags (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table topic_tags (
	topic_id int unsigned not null,
	tag_id int unsigned not null,
	foreign key (topic_id) references topics(id),
	foreign key (tag_id) references tags(id)
);
