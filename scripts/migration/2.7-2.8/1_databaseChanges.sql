alter table people drop authenticationMethod;
alter table people drop password;

drop table legislation_tags;
drop table tags;

create table meetings(
    id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    meetingDate      date         not null,
    start            datetime,
    end              datetime,
    eventId          varchar(128),
    location         varchar(256),
    htmlLink         varchar(256),
    foreign key (committee_id) references committees(id)
);

insert into meetings (committee_id, meetingDate, eventId)
select f.committee_id, f.meetingDate, f.eventId
from meetingFiles f
group by f.committee_id, f.meetingDate, f.eventId;

alter table meetingFiles add meeting_id int unsigned not null after committee_id;

update meetingFiles f
join meetings       m on (f.committee_id=m.committee_id and f.meetingDate=m.meetingDate and f.eventId=m.eventId)
set f.meeting_id=m.id;

update meetingFiles f
join meetings       m on (f.committee_id=m.committee_id and f.meetingDate=m.meetingDate and f.eventId is null and m.eventId is null)
set f.meeting_id=m.id;

alter table meetingFiles add foreign key (meeting_id) references meetings(id);
