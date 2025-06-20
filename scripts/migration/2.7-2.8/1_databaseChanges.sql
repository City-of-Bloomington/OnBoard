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
    created          datetime     not null default CURRENT_TIMESTAMP,
    updated          datetime     not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    title            varchar(256),
    eventId          varchar(1024),
    location         varchar(256),
    htmlLink         varchar(1024),
    attendanceNotes  varchar(256),
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
alter table committees add syncToken varchar(64) after calendarId;
alter table committees add synced    datetime    after syncToken;

create table meeting_attendance (
    meeting_id int unsigned not null,
    member_id  int unsigned not null,
    status     varchar(16)  not null,
    foreign key (meeting_id) references meetings(id),
    foreign key ( member_id) references  members(id)
);

alter table people modify username varchar(128);

alter table legislationFiles add indexed timestamp;
alter table reports          add indexed timestamp;
