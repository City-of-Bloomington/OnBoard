alter table meetings drop meetingDate;
alter table meetings rename column datetime to start;
alter table meetings modify start datetime not null;
