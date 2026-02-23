alter table meetingFiles     add    url              varchar(255) after title;
alter table meetingFiles     modify internalFilename varchar(128);

alter table applicantFiles   add    url              varchar(255) after id;
alter table applicantFiles   modify internalFilename varchar(128);
alter table applicantFiles   modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table applicantFiles   add    indexed          timestamp    after created;
alter table applicantFiles   add    updated_by       int unsigned after updated;
alter table applicantFiles   add    foreign key (updated_by) references people(id);

alter table legislationFiles add    url              varchar(255) after legislation_id;
alter table legislationFiles modify internalFilename varchar(128);
alter table legislationFiles modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table legislationFiles modify indexed          timestamp    after created;
alter table legislationFiles add    updated_by       int unsigned;
alter table legislationFiles add    foreign key (updated_by) references people(id);

alter table reports          add    url              varchar(255) after reportDate;
alter table reports          modify internalFilename varchar(128);
alter table reports          modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table reports          modify indexed          timestamp    after created;
alter table reports          add    updated_by       int unsigned;
alter table reports          add    foreign key (updated_by) references people(id);
