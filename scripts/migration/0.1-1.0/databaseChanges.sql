alter table people add username varchar(40) unique;
alter table people add password varchar(40);
alter table people add authenticationMethod varchar(40);
alter table people add role varchar(30);
alter table people drop address;
alter table people drop city;
alter table people drop zipcode;
alter table people drop birthdate;

update users u,people p
set p.username=u.username, p.authenticationMethod=u.authenticationMethod
where u.person_id=p.id;

update people p,users,user_roles,roles
set p.role=roles.name
where p.id=users.person_id
and users.id=user_roles.user_id
and user_roles.role_id=roles.id
and p.username is not null;

update people set role='Staff' where role='Clerk';

drop table user_roles;
drop table roles;
drop table users;

drop table people_private_fields;

-- We did not actually have any phoneNumbers.  This feature was never implemented
drop table phoneNumbers;

rename table officers to offices;

alter table seats add startDate date;
alter table seats add endDate   date;
update seats s set s.startDate=(select min(term_start) from terms where terms.seat_id=s.id);
