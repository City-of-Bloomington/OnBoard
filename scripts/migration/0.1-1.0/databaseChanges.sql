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

alter table committees change dateFormed yearFormed year(4);
alter table committees add statuteUrl      varchar(128) after statuteReference;
alter table committees add contactInfo     text;
alter table committees add meetingSchedule text;

alter table seats modify appointer_id int unsigned;
alter table seats modify maxCurrentTerms tinyint unsigned;

-- 2014-04-23
alter table seats add requirements text;

-- Format all the existing requirements as markdown text and save them
-- in the new requirements field for each seat
update seats s
join (
	select y.id, group_concat(concat('* ', r.text) separator '\n') as text
	from seats y
	join seat_requirements sr on y.id=sr.seat_id
	join requirements r on sr.requirement_id=r.id
	group by y.id
) x on s.id=x.id
set s.requirements=x.text;

drop table seat_requirements;
drop table requirements;
