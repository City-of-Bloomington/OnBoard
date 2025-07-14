create table people_emails (
    id        int unsigned not null primary key auto_increment,
    person_id int unsigned not null,
    email     varchar(128) not null unique,
    main      boolean,
    unique  key (person_id, email),
    foreign key (person_id) references people(id)
);

create table people_phones (
    id        int unsigned not null primary key auto_increment,
    person_id int unsigned not null,
    number    varchar(32)  not null,
    main      boolean,
    unique  key (person_id, number),
    foreign key (person_id) references people(id)
);

insert into people_emails (person_id, email, main) select id, email, 1
from people
where email is not null
  and email !='';

insert into people_phones (person_id, number, main) select id, phone, 1
from people
where phone is not null
  and phone !='';

alter table people drop email;
alter table people drop phone;

alter table applications add referredFrom   varchar(128);
alter table applications add referredOther  varchar(128);
alter table applications add interest       text;
alter table applications add qualifications text;

update applications a
join applicants t on a.applicant_id=t.id
set a.referredFrom=t.referredFrom, a.referredOther=t.referredOther,
    a.interest=t.interest, a.qualifications=t.qualifications;

alter table applicants drop referredFrom;
alter table applicants drop referredOther;
alter table applicants drop interest;
alter table applicants drop qualifications;
