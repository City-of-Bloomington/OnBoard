alter table meetings drop meetingDate;
alter table meetings modify start datetime not null;
alter table meetings modify   end datetime not null;

alter table meetingFiles drop foreign key meetingFiles_ibfk_1;
alter table meetingFiles drop committee_id;
alter table meetingFiles drop meetingDate;
alter table meetingFiles drop eventId;
