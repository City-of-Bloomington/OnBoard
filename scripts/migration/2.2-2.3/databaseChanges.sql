alter table committees   add code    varchar(8)   after statutoryName;
alter table meetingFiles add title   varchar(32)  after type;
alter table people       add website varchar(128) after zip;
