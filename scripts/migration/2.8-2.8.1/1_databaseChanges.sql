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

alter table people drop foreign key people_ibfk_1;
alter table people drop gender;
alter table people drop race_id;
alter table people drop email;
alter table people drop phone;
alter table people add citylimits boolean      after zip;
alter table people add occupation varchar(128) after citylimits;
alter table people add created    datetime  not null default CURRENT_TIMESTAMP;
alter table people add updated    timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

alter table applications add referredFrom   varchar(128);
alter table applications add referredOther  varchar(128);
alter table applications add interest       text;
alter table applications add qualifications text;
alter table applications add person_id      int unsigned after applicant_id;
alter table applications add foreign key (person_id) references people(id);

alter table applicantFiles add person_id int unsigned after applicant_id;
alter table applicantFiles add foreign key (person_id) references people(id);
alter table applicantFiles modify updated timestamp not null;

update applications a
join applicants t on a.applicant_id=t.id
set a.referredFrom=t.referredFrom, a.referredOther=t.referredOther,
    a.interest=t.interest, a.qualifications=t.qualifications;

alter table applicants drop referredFrom;
alter table applicants drop referredOther;
alter table applicants drop interest;
alter table applicants drop qualifications;

drop table races;

create table email_queue (
    id           int unsigned not null primary key auto_increment,
    emailfrom    varchar(128) not null,
    emailto      varchar(128) not null,
    cc           varchar(128),
    bcc          varchar(128),
    subject      varchar(128) not null,
    body         text,
    event        varchar(128),
    committee_id int unsigned,
    created      datetime not null default CURRENT_TIMESTAMP,
    sent         datetime,
    foreign key (committee_id) references committees(id)
);

create table notification_definitions (
    id           int unsigned not null primary key auto_increment,
    event        varchar(128) not null,
    committee_id int unsigned,
    subject      varchar(128) not null,
    body         text         not null,
    foreign key (committee_id) references committees(id),
    unique (event, committee_id)
);

create table notification_subscriptions (
    id            int unsigned not null primary key auto_increment,
    person_id     int unsigned not null,
    committee_id  int unsigned not null,
    event         varchar(128) not null,
    unique (person_id, committee_id, event),
    foreign key (   person_id) references people(id),
    foreign key (committee_id) references committees(id)
);

create table committee_notes (
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    note text,
    created  timestamp not null default CURRENT_TIMESTAMP,
    modified timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    foreign key (committee_id) references committees(id),
    foreign key (   person_id) references     people(id)
);
