alter table applicantFiles drop foreign key applicantFiles_ibfk_1;
alter table applications   drop foreign key applications_ibfk_2;
alter table applicantFiles drop applicant_id;
alter table applications   drop applicant_id;
drop table applicants;
