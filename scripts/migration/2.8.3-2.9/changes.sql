alter table meetingFiles     add    url              text after title;
alter table meetingFiles     modify internalFilename varchar(128);
alter table meetingFiles     modify filename         varchar(256);
alter table meetingFiles     modify mime_type        varchar(128);

alter table applicantFiles   add    url              text after id;
alter table applicantFiles   modify internalFilename varchar(128);
alter table applicantFiles   modify filename         varchar(256);
alter table applicantFiles   modify mime_type        varchar(128);
alter table applicantFiles   modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table applicantFiles   add    indexed          timestamp    after created;
alter table applicantFiles   add    updated_by       int unsigned after updated;
alter table applicantFiles   add    foreign key (updated_by) references people(id);

alter table legislationFiles add    url              text after legislation_id;
alter table legislationFiles modify internalFilename varchar(128);
alter table legislationFiles modify filename         varchar(256);
alter table legislationFiles modify mime_type        varchar(128);
alter table legislationFiles modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table legislationFiles modify indexed          timestamp    after created;
alter table legislationFiles add    updated_by       int unsigned;
alter table legislationFiles add    foreign key (updated_by) references people(id);

alter table reports          add    url              text after reportDate;
alter table reports          modify internalFilename varchar(128);
alter table reports          modify filename         varchar(256);
alter table reports          modify mime_type        varchar(128);
alter table reports          modify created          timestamp not null default CURRENT_TIMESTAMP;
alter table reports          modify indexed          timestamp    after created;
alter table reports          add    updated_by       int unsigned;
alter table reports          add    foreign key (updated_by) references people(id);

alter table committee_notes add archived timestamp;

create table committeeFiles(
    id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    type             varchar(16)  not null,
    title            varchar(64),
    url              text,
    internalFilename varchar(128),
    filename         varchar(256),
    mime_type        varchar(128),
    created          timestamp    not null default CURRENT_TIMESTAMP,
    indexed          timestamp,
    updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    updated_by       int unsigned,
    foreign key (committee_id) references committees(id),
    foreign key (updated_by  ) references people(id)
);
