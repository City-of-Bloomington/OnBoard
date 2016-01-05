alter table committees add type enum('seated', 'open') not null default 'seated';
update committees set type='seated';
