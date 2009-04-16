delete from user_roles;
delete from users;
delete from people;

insert people
select id,firstname,lastname,email,address,city,zipcode,about,gender,race_id,birthdate
from committee_manager.users;

insert users
select id,id,username,password,authenticationMethod
from committee_manager.users
where username is not null;

insert user_roles select * from committee_manager.user_roles;
insert committees select * from committee_manager.committees;

delete from appointers;
insert appointers select * from committee_manager.appointers;

insert requirements select * from committee_manager.requirements;

delete from seats;
insert seats select * from committee_manager.seats;
insert seat_requirements select * from committee_manager.seat_requirements;

insert terms select * from committee_manager.members;

delete from topicTypes;
insert topicTypes select * from committee_manager.topicTypes;

delete from topics;
insert topics select * from committee_manager.topics;

delete from voteTypes;
insert voteTypes select * from committee_manager.voteTypes;

delete from votes;
insert votes select * from committee_manager.votes;

delete from votingRecords;
insert votingRecords select * from committee_manager.votingRecords;

delete from tags;
insert tags select * from committee_manager.tags;

delete from topic_tags;
insert topic_tags select * from committee_manager.topic_tags;

