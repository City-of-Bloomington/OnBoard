alter table meetingFiles add updated_by int unsigned;
alter table meetingFiles add foreign key (updated_by) references people(id);

