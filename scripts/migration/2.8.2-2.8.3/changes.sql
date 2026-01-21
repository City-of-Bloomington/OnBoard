create table people_addresses (
    id        int unsigned not null primary key auto_increment,
    person_id int unsigned not null,
    type      enum('Home', 'Mailing') not null default 'Home',
	address   varchar(128) not null,
	city      varchar(32),
	state     varchar(8),
	zip       varchar(8),
	x         int,
	y         int,
	foreign key (person_id) references people(id)
);

insert into people_addresses
 (person_id, type,   address, city, state, zip)
select id, 'Mailing', address, city, state, zip
from people
where address is not null;

alter table people drop address;
alter table people drop city;
alter table people drop state;
alter table people drop zip;
alter table people drop citylimits;
