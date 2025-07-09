alter table applications add person_id int unsigned after applicant_id;
alter table applications add foreign key (person_id) references people(id);

alter table people add citylimits boolean      after zip;
alter table people add occupation varchar(128) after citylimits;

-- Applicants who already have a person record
update people p
join applicants a on p.email=a.email
set p.citylimits=a.citylimits, p.occupation=a.occupation;

-- Applicants who still need to have a person record created
insert people (firstname, lastname, email, phone, address, city, zip, citylimits, occupation)
select a.firstname, a.lastname, a.email, a.phone, a.address, a.city, a.zip, a.citylimits, a.occupation
from applicants a
left join people p on a.email=p.email
where p.id is null;
