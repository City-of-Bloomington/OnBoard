create table committee_liasons (
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    primary key (committee_id, person_id),
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people(id)
);

alter table people add phone varchar(32) after email;