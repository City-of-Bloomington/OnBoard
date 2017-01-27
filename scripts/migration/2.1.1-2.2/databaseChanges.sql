rename table media to applicantFiles;
alter table applicantFiles modify internalFilename varchar(128) not null;
update applicantFiles set internalFilename=concat(date_format(uploaded, '%Y/%m/%d/'), internalFilename);
alter table applicantFiles change uploaded updated timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;
alter table applicantFiles add created datetime not null default CURRENT_TIMESTAMP after mime_type;
update applicantMedia set created=updated;

alter table committees add calendarId varchar(128) after endDate;

create table meetingFiles(
	id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    meetingDate      date         not null,
    eventId          varchar(128),
    type             varchar(16)  not null,
	internalFilename varchar(128) not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	created          datetime     not null default CURRENT_TIMESTAMP,
	updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	foreign key (committee_id) references committees(id)
);
