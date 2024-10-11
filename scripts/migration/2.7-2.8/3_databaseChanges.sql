alter table meetings drop meetingDate;
alter table meetings modify start datetime not null;
alter table meetings modify   end datetime not null;
