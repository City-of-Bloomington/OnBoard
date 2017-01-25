rename table media to applicantMedia;
alter table applicantMedia modify internalFilename varchar(128) not null;
update applicantMedia set internalFilename=concat(date_format(uploaded, '%Y/%m/%d/'), internalFilename);
alter table applicantMedia change uploaded updated timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;
alter table applicantMedia add created datetime not null default CURRENT_TIMESTAMP after mime_type;
update applicantMedia set created=updated;
