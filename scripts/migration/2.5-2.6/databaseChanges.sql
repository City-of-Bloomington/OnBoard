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

