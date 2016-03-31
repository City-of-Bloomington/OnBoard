alter table committee_liaisons drop foreign key committee_liaisons_ibfk_1;
alter table committee_liaisons drop foreign key committee_liaisons_ibfk_2;
alter table committee_liaisons drop primary key;

rename table committee_liaisons to liaisons;
alter table liaisons add id int unsigned not null primary key auto_increment first;
alter table liaisons add type enum('legal', 'departmental') not null default 'departmental' after id;
alter table liaisons add foreign key (committee_id) references committees(id);
alter table liaisons add foreign key (person_id)    references people(id);

alter table seats add voting boolean not null default 1;

create table committeeStatutes(
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    citation varchar(128) not null,
    url      varchar(128) not null,
    foreign key (committee_id) references committees(id)
);


insert into committeeStatutes (committee_id, citation, url)
select id, statuteReference, statuteUrl from committees where statuteUrl is not null;

alter table committees drop statuteReference;
alter table committees drop statuteUrl;

alter table committees add endDate date after yearFormed;
