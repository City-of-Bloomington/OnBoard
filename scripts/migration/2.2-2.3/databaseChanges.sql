alter table committees   add code    varchar(8)   after statutoryName;
alter table meetingFiles add title   varchar(32)  after type;
alter table people       add website varchar(128) after zip;
alter table people      drop about;

create table legislationTypes (
    id   int unsigned not null primary key auto_increment,
    name varchar(32)  not null
);

create table legislationActionTypes (
    id       int unsigned     not null primary key auto_increment,
    name     varchar(32)      not null,
    ordering tinyint unsigned not null
);

create table legislation (
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    type_id      int unsigned not null,
    number       varchar(24)  not null,
    title        text         not null,
    synopsis     text,
    foreign key (committee_id) references committees      (id),
    foreign key (type_id     ) references legislationTypes(id)
);

create table legislationActions (
    id             int unsigned not null primary key auto_increment,
    legislation_id int unsigned not null,
    type_id        int unsigned not null,
    actionDate     date         not null,
    outcome enum('pass', 'fail'),
    foreign key (legislation_id) references legislation(id),
    foreign key (type_id       ) references legislationActionTypes(id)
);

create table legislationFiles (
    id               int unsigned not null primary key auto_increment,
    legislation_id   int unsigned not null,
	internalFilename varchar(128) not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	created          datetime     not null /*!50700 default CURRENT_TIMESTAMP */,
	updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	foreign key (legislation_id) references (legislation(id)
);
