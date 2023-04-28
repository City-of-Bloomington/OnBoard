alter table meetingFiles add updated_by int unsigned;
alter table meetingFiles add foreign key (updated_by) references people(id);

alter table meetingFiles add    indexed timestamp after created;
alter table meetingFiles modify created timestamp not null default CURRENT_TIMESTAMP;
