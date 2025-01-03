-- There are a few meetings with duplicate eventIds
-- The duplicate meetings need to be removed (combined)
-- Update the meetingFiles to point to one of the meetings and delete the other
select eventId, count(*) as c
from meetings
where eventId is not null
group by eventId having c>1;


select m.id,
       m.committee_id,
       m.start,
       m.end,
       m.eventId,
       f.type,
       f.id as file_id
from meetings m
join meetingFiles f on m.id=f.meeting_id
where m.eventId='ikvurg3vj4q8h0qphbul2ffa20_20040108T003000Z';

update meetingFiles set meeting_id=? where id=?;
delete from meetings where id=?;

+---------------------------------------------+---+
| eventId                                     | c |
+---------------------------------------------+---+
| ikvurg3vj4q8h0qphbul2ffa20_20040108T003000Z | 2 |
| 5f1fsktlnc9vtvq699sdudalpm                  | 2 |
| 867c43th8e2gj2svro2tuiv5v4_20200826T223000Z | 2 |
| 4od4da9mcg94vjjhmvojsc81di                  | 2 |
| m59qjmcrr653qlb9qmrp3toboc_20210224T233000Z | 2 |
| lulvf4kqdei1dki2pche8kv4qk_20210224T150000Z | 2 |
| 2sv0asqa8ijqplmf06phi3d2nf                  | 2 |
| 3rpenajcpulod6c8g5j4pt7ekj                  | 2 |
| 2vr560ttu85e3mls95gp0a1pu3                  | 2 |
| 6ojpbr2vrj3mopl90navr8r599_20240416T200000Z | 2 |
+---------------------------------------------+---+



select committee_id, eventId, count(distinct meetingDate) as dates
from meetingFiles
where eventId is not null
group by committee_id, eventId
having dates>1;

select id, committee_id, meetingDate, eventId, type
from meetingFiles
where eventId='ikvurg3vj4q8h0qphbul2ffa20_20040108T003000Z';

where id in (7981, 7987, 7993, 7994)
