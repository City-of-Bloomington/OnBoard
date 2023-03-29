alter table meetingFiles add updated_by int unsigned;
alter table meetingFiles add foreign key (updated_by) references people(id);

alter table meetingFiles add indexed datetime after created;
alter table meetingFiles modify created not null default CURRENT_TIMESTAMP;
