rename table committee_liasons to committee_liaisons;

alter table departments drop email;
alter table departments drop phone;

alter table committees add applicationLifetime tinyint unsigned not null default 90;
update committees set applicationLifetime=90;

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
	created  timestamp not null default CURRENT_TIMESTAMP,
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
