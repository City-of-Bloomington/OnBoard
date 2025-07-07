alter table applications add referredFrom   varchar(128);
alter table applications add referredOther  varchar(128);
alter table applications add interest       text;
alter table applications add qualifications text;

update applications a
join applicants t on a.applicant_id=t.id
set a.referredFrom=t.referredFrom, a.referredOther=t.referredOther,
    a.interest=t.interest, a.qualifications=t.qualifications;

alter table applicants drop referredFrom;
alter table applicants drop referredOther;
alter table applicants drop interest;
alter table applicants drop qualifications;
