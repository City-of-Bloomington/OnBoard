create table committee_liasons (
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    primary key (committee_id, person_id),
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people(id)
);

alter table people add phone varchar(32) after email;

create table departments (
    id int unsigned not null primary key auto_increment,
    name  varchar(128) not null unique,
    email varchar(128),
    phone varchar(32)
);
create table committee_departments (
    committee_id  int unsigned not null,
    department_id int unsigned not null,
    primary key (committee_id, department_id),
    foreign key (committee_id)  references committees (id),
    foreign key (department_id) references departments(id)
);

alter table committees add termEndWarningDays tinyint unsigned not null default 0;

alter table seats add code varchar(16) after type;
