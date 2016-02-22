-- @copyright 2006-2016 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
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

create table departments (
    id int unsigned not null primary key auto_increment,
    name  varchar(128) not null unique
);

create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	email varchar(128) unique,
	phone varchar(32),
	about text,
	gender enum('male','female'),
	race_id int unsigned,
	username varchar(40) unique,
	password varchar(40),
	authenticationMethod varchar(40),
	role varchar(30),
	foreign key (race_id) references races(id)
);

create table committees (
	id int unsigned not null primary key auto_increment,
	type enum('seated', 'open') not null default 'seated',
	name varchar(128) not null,
	statutoryName varchar(128),
	statuteReference varchar(128),
	statuteUrl varchar(128),
	yearFormed year(4),
	website varchar(128),
	email   varchar(128),
	phone   varchar(128),
	address varchar(128),
	city    varchar(128),
	state   varchar(32),
	zip     varchar(32),
    description     text,
	contactInfo     text,
	meetingSchedule text,
	termEndWarningDays  tinyint unsigned not null default 0,
	applicationLifetime tinyint unsigned not null default 90
);

create table committee_departments (
    committee_id  int unsigned not null,
    department_id int unsigned not null,
    primary key (committee_id, department_id),
    foreign key (committee_id)  references committees (id),
    foreign key (department_id) references departments(id)
);

create table appointers (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null unique
);
insert appointers values(1,'Elected');

--
-- Begin 2.0 changes
--
create table seats (
    id int unsigned not null primary key auto_increment,
    type enum('termed', 'open') not null default 'termed',
    code varchar(16),
    name varchar(128) not null,
	committee_id int unsigned not null,
	appointer_id int unsigned,
    startDate date,
    endDate   date,
    requirements text,
    termLength varchar(32),
	foreign key (committee_id) references committees(id),
	foreign key (appointer_id) references appointers(id)
);

create table terms (
    id      int unsigned not null primary key auto_increment,
	seat_id int unsigned,
	startDate date not null,
	endDate   date not null,
	foreign key (seat_id) references seats(id)
);

create table members (
	id int unsigned not null primary key auto_increment,
	committee_id int unsigned not null,
	seat_id      int unsigned,
	term_id      int unsigned,
	person_id    int unsigned not null,
	startDate date,
	endDate   date,
	foreign key (committee_id) references committees(id),
	foreign key (seat_id)      references seats     (id),
	foreign key (term_id)      references terms     (id),
	foreign key (person_id)    references people    (id)
);

create table committee_liaisons (
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    primary key (committee_id, person_id),
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people(id)
);

create table applicants (
    id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname  varchar(128) not null,
	email     varchar(128),
	phone     varchar(32),
	address   varchar(128),
	city      varchar(128),
	zip       varchar(5),
	citylimits     boolean,
	occupation     varchar(128),
	referredFrom   varchar(128),
	referredOther  varchar(128),
	interest       text,
	qualifications text,
	created  datetime,
	modified timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);

create table applications (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    applicant_id int unsigned not null,
    created  timestamp not null default CURRENT_TIMESTAMP,
    archived datetime,
    foreign key (committee_id) references committees(id),
    foreign key (applicant_id) references applicants(id)
);

create table media (
	id int unsigned not null primary key auto_increment,
	internalFilename varchar(50)  not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	uploaded         datetime     not null,
	applicant_id     int unsigned not null,
	foreign key (applicant_id) references applicants(id)
);

--
-- End 2.0 changes
--

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
