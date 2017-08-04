alter table committees   add code        varchar(8)   after statutoryName;
alter table committees   add legislative boolean;
alter table meetingFiles add title       varchar(32)  after type;
alter table people       add website     varchar(128) after zip;
alter table people      drop about;

create table legislationTypes (
    id      int unsigned not null primary key auto_increment,
    name    varchar(32)  not null,
    subtype boolean      not null default 0
);

create table legislationActionTypes (
    id       int unsigned     not null primary key auto_increment,
    name     varchar(32)      not null,
    ordering tinyint unsigned not null
);

create table legislation (
    id           int      unsigned not null primary key auto_increment,
    committee_id int      unsigned not null,
    type_id      int      unsigned not null,
    parent_id    int      unsigned,
    year         smallint unsigned not null,
    number       varchar(24)       not null,
    title        text              not null,
    synopsis     text,
    foreign key (committee_id) references committees      (id),
    foreign key (type_id     ) references legislationTypes(id),
    foreign key (parent_id   ) references legislation     (id)
);

create table legislationActions (
    id             int unsigned not null primary key auto_increment,
    legislation_id int unsigned not null,
    type_id        int unsigned not null,
    actionDate     date         not null,
    outcome        varchar(16),
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

create table tags (
    id   int unsigned not null primary key auto_increment,
    name varchar(128) not null
);

create table legislation_tags (
    legislation_id int unsigned not null,
    tag_id         int unsigned not null,
    foreign key (legislation_id) references legislation(id),
    foreign key (tag_id        ) references tags       (id)
);
