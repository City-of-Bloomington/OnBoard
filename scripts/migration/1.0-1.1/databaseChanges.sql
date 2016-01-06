alter table committees add type enum('seated', 'open') not null default 'seated';
update committees set type='seated';

alter table terms add committee_id int unsigned not null after id;
update terms t, seats s set t.committee_id=s.committee_id where s.id=t.seat_id;
alter table terms add foreign key (committee_id) references committees(id);

alter table terms modify seat_id int unsigned;