-- @copyright 2006-2025 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE
create table departments (
    id    int unsigned not null primary key auto_increment,
    name  varchar(128) not null unique
);

create table people (
	id         int unsigned not null primary key auto_increment,
	firstname  varchar(128) not null,
	lastname   varchar(128) not null,
	address    varchar(128),
	city       varchar(32),
	state      varchar(8),
	zip        varchar(8),
    citylimits boolean,
    occupation varchar(128),
	website    varchar(128),
	username             varchar(128) unique,
	role                 varchar(30),
	department_id        int unsigned,
    created    datetime  not null default CURRENT_TIMESTAMP,
    updated    timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
	foreign key (race_id      ) references races(id),
	foreign key (department_id) references departments(id)
);

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
    unique  key (person_id, phone),
    foreign key (person_id) references people(id)
);

create table committees (
    id            int unsigned  not null primary key auto_increment,
    type enum('seated', 'open') not null default 'seated',
    name          varchar(128)  not null,
    statutoryName varchar(128),
    code          varchar(8),
    yearFormed    year(4),
    endDate       date,
    calendarId    varchar(128),
    syncToken     varchar(64),
    website       varchar(128),
    videoArchive  varchar(128),
    email         varchar(128),
    phone         varchar(128),
    address       varchar(128),
    city          varchar(128),
    state         varchar(32),
    zip           varchar(32),
    description     text,
    meetingSchedule text,
    termEndWarningDays  tinyint unsigned not null default 0,
    applicationLifetime tinyint unsigned not null default 90,
    legislative    boolean,
    alternates     boolean
);

create table committeeStatutes(
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    citation varchar(128) not null,
    url      varchar(128) not null,
    foreign key (committee_id) references committees(id)
);

create table committee_departments (
    committee_id  int unsigned not null,
    department_id int unsigned not null,
    primary key (committee_id, department_id),
    foreign key (committee_id)  references committees (id),
    foreign key (department_id) references departments(id)
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

create table appointers (
    id int unsigned not null primary key auto_increment,
    name varchar(128) not null unique
);
insert appointers values(1,'Elected');

create table seats (
    id                int unsigned not null primary key auto_increment,
    type              enum('termed', 'open') not null default 'termed',
    code              varchar(16),
    name              varchar(128) not null,
    committee_id      int unsigned not null,
    appointer_id      int unsigned,
    startDate         date,
    endDate           date,
    requirements      text,
    termLength        varchar(32),
    termModifier      varchar(32),
    voting            boolean not null default 1,
    takesApplications boolean not null default 0,
    foreign key (committee_id) references committees(id),
    foreign key (appointer_id) references appointers(id)
);

create table terms (
    id      int unsigned not null primary key auto_increment,
    seat_id int unsigned,
    startDate date not null,
    endDate   date not null,
    foreign key (seat_id) references seats(id)
);

create table members (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    seat_id      int unsigned,
    term_id      int unsigned,
    person_id    int unsigned not null,
    startDate date,
    endDate   date,
    foreign key (committee_id) references committees(id),
    foreign key (seat_id)      references seats     (id),
    foreign key (term_id)      references terms     (id),
    foreign key (person_id)    references people    (id)
);

create table alternates (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    seat_id      int unsigned,
    term_id      int unsigned,
    person_id    int unsigned not null,
    startDate date,
    endDate   date,
    foreign key (committee_id) references committees(id),
    foreign key (seat_id)      references seats     (id),
    foreign key (term_id)      references terms     (id),
    foreign key (person_id)    references people    (id)
);

create table liaisons (
    id int unsigned not null primary key auto_increment,
    type enum('legal', 'departmental') not null default 'departmental',
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people(id)
);

create table applications (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    created  timestamp not null default CURRENT_TIMESTAMP,
    archived datetime,
    referredFrom   varchar(128),
    referredOther  varchar(128),
    interest       text,
    qualifications text,
    foreign key (committee_id) references committees(id),
    foreign key (person_id   ) references people(id)
);

create table applicantFiles (
    id int unsigned not null primary key auto_increment,
    internalFilename varchar(128) not null,
    filename         varchar(128) not null,
    mime_type        varchar(128) not null,
    created          datetime     not null default CURRENT_TIMESTAMP,
    updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    person_id        int unsigned not null,
    foreign key (person_id) references people(id)
);

create table offices (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    person_id int unsigned not null,
    title varchar(128) not null,
    startDate date not null,
    endDate date,
    foreign key (committee_id) references committees(id),
    foreign key (person_id) references people(id)
);

create table siteContent (
    label varchar(128) not null primary key,
    content text
);

create table meetings(
    id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    start            datetime     not null,
    end              datetime     not null,
    created          datetime     not null default CURRENT_TIMESTAMP,
    updated          datetime     not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    title            varchar(256),
    eventId          varchar(1024),
    location         varchar(256),
    htmlLink         varchar(1024),
    attendanceNotes  varchar(256),
    foreign key (committee_id) references committees(id)
);

create table meetingFiles(
    id               int unsigned not null primary key auto_increment,
    meeting_id       int unsigned not null,
    type             varchar(16)  not null,
    title            varchar(64),
    internalFilename varchar(128) not null,
    filename         varchar(128) not null,
    mime_type        varchar(128) not null,
    created          timestamp    not null default CURRENT_TIMESTAMP,
    updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    indexed          timestamp,
    foreign key (committee_id) references committees(id),
    foreign key (  meeting_id) references   meetings(id)
);

create table meeting_attendance (
    meeting_id int unsigned not null,
    member_id  int unsigned not null,
    status     varchar(16)  not null,
    foreign key (meeting_id) references meetings(id),
    foreign key ( member_id) references  members(id)
);

create table committeeHistory(
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    date         timestamp    not null default CURRENT_TIMESTAMP,
    tablename    varchar(32)  not null,
    action       varchar(32)  not null,
    changes      text,
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people    (id)
);

create table legislationTypes (
    id      int unsigned not null primary key auto_increment,
    name    varchar(32)  not null unique,
    subtype boolean      not null default 0
);

create table legislationActionTypes (
    id       int unsigned     not null primary key auto_increment,
    name     varchar(32)      not null unique,
    ordering tinyint unsigned not null
);

create table legislationStatuses (
    id     int unsigned not null primary key auto_increment,
    name   varchar(64)  not null unique,
    active boolean      not null default 0
);

create table legislation (
    id           int      unsigned not null primary key auto_increment,
    committee_id int      unsigned not null,
    type_id      int      unsigned not null,
    parent_id    int      unsigned,
    status_id    int      unsigned,
    year         smallint unsigned not null,
    number       varchar(24)       not null,
    title        text              not null,
    synopsis     text,
    notes        text,
    amendsCode   boolean           not null default 0,
    foreign key (committee_id) references committees         (id),
    foreign key (type_id     ) references legislationTypes   (id),
    foreign key (parent_id   ) references legislation        (id),
    foreign key (status_id   ) references legislationStatuses(id)
);

create table legislationActions (
    id             int unsigned not null primary key auto_increment,
    legislation_id int unsigned not null,
    type_id        int unsigned not null,
    actionDate     date         not null,
    outcome        varchar(64),
    vote           varchar(255),
    foreign key (legislation_id) references legislation(id),
    foreign key (type_id       ) references legislationActionTypes(id)
);

create table legislationFiles (
    id               int unsigned not null primary key auto_increment,
    legislation_id   int unsigned not null,
    internalFilename varchar(128) not null,
    filename         varchar(128) not null,
    mime_type        varchar(128) not null,
    created          datetime     not null default CURRENT_TIMESTAMP,
    updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    indexed          timestamp,
    foreign key (legislation_id) references legislation(id)
);

create table reports (
    id int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    title            varchar(128) not null,
    reportDate       date         not null,
    internalFilename varchar(128) not null,
    filename         varchar(128) not null,
    mime_type        varchar(128) not null,
    created          datetime     not null default CURRENT_TIMESTAMP,
    updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    indexed          timestamp,
    foreign key (committee_id) references committees(id)
);

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
