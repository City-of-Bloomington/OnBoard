alter table committees add email   varchar(128) after website;
alter table committees add phone   varchar(128) after email;
alter table committees add address varchar(128) after phone;
alter table committees add city    varchar(128) after address;
alter table committees add state   varchar(32)  after city;
alter table committees add zip     varchar(32)  after state;
