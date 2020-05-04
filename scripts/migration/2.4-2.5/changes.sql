alter table legislationStatuses add active boolean not null default 0;
update legislationStatuses set active=1 where name in (
    'Adopted',
    'Denied by Plan Commission',
    'Failed',
    'First Reading',
    'Introduced',
    'Not Introduced',
    'Passed',
    'Referred to Committee',
    'Second Reading',
    'Vetoed',
    'Withdrawn'
);
