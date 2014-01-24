alter table people add username varchar(40) unique;
alter table people add password varchar(40);
alter table people add authenticationMethod varchar(40);
alter table people add role varchar(30);

update users u,people p
set p.username=u.username, p.authenticationMethod=u.authenticationMethod
where u.person_id=p.id;

update people p,users,user_roles,roles
set p.role=roles.name
where p.id=users.person_id
and users.id=user_roles.user_id
and user_roles.role_id=roles.id
and p.username is not null;

drop table user_roles;
drop table roles;
drop table users;

alter table people add privateFields varchar(128);

update people p,
(
	select person_id,group_concat(fieldname separator ',') as privateFields
	from people_private_fields
	group by person_id
) temp
set p.privateFields=temp.privateFields
where p.id=temp.person_id;

drop table people_private_fields;

-- We did not actually have any phoneNumbers.  This feature was never implemented
drop table phoneNumbers;

rename table officers to offices;
