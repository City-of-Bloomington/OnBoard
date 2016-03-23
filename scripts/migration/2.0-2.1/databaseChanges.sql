alter table committee_liaisons drop foreign key committee_liaisons_ibfk_1;
alter table committee_liaisons drop foreign key committee_liaisons_ibfk_2;
alter table committee_liaisons drop primary key;

rename table committee_liaisons to liaisons;
alter table liaisons add id int unsigned not null primary key auto_increment first;
alter table liaisons add type enum('legal', 'departmental') not null default 'departmental' after id;
alter table liaisons add foreign key (committee_id) references committees(id);
alter table liaisons add foreign key (person_id)    references people(id);
