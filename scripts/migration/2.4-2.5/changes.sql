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

alter table people add department_id int unsigned;
alter table people add foreign key (department_id) references departments(id);

update people set role='Clerk'     where role='meetingFileUploader';
update people set department_id=6  where role='Appointer';
update people set department_id=8  where role='Reviewer';
update people set role='Appointer' where role='Reviewer';

alter table seats add termModifier varchar(32) after termLength;
