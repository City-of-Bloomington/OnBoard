<?php
/**
 * @copyright 2020-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$ROUTES = new \Aura\Router\RouterContainer(BASE_URI);
$map    = $ROUTES->getMap();
$map->tokens(['alternate_id'             => '\d+',
              'applicantFile_id'         => '\d+',
              'applicant_id'             => '\d+',
              'application_id'           => '\d+',
              'appointer_id'             => '\d+',
              'committee_id'             => '\d+',
              'committeeStatute_id'      => '\d+',
              'department_id'            => '\d+',
              'legislation_id'           => '\d+',
              'legislationAction_id'     => '\d+',
              'legislationActionType_id' => '\d+',
              'legislationFile_id'       => '\d+',
              'legislationStatus_id'     => '\d+',
              'legislationType_id'       => '\d+',
              'liaison_id'               => '\d+',
              'meeting_id'               => '\d+',
              'meetingFile_id'           => '\d+',
              'member_id'                => '\d+',
              'office_id'                => '\d+',
              'person_id'                => '\d+',
              'race_id'                  => '\d+',
              'report_id'                => '\d+',
              'seat_id'                  => '\d+',
              'term_id'                  => '\d+',
              'id'                       => '\d+']);

$map->attach('home.', '/', function ($r) {
    $r->get('calendarhook', 'notifications', Web\Meetings\CalendarHook\Controller::class)->allows(['POST']);
    $r->get ('index', '', Web\Committees\List\Controller::class);
});

$map->attach('login.', '/login', function ($r) {
    $r->get('logout',   '/logout'  , Web\Auth\Logout\Controller::class);
    $r->get('accessin', '/accessin', Web\Auth\AccessIndiana\Controller::class)->allows(['POST']);
    $r->get('index',    ''         , Web\Auth\Oidc\Controller::class);
});

$map->attach('alternates.', '/alternates', function ($r) {
    $r->get('add',    '/add'        ,           Web\Alternates\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{alternate_id}/update', Web\Alternates\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{alternate_id}/delete', Web\Alternates\Delete\Controller::class);
    $r->get('view',   '/{alternate_id}',        Web\Alternates\Info\Controller::class);
});

$map->attach('applicantFiles.', '/applicantFiles', function ($r) {
    $r->get('download', '/{applicantFile_id}/download', Web\Applicants\Files\Download\Controller::class);
    $r->get('delete',   '/{applicantFile_id}/delete'  , Web\Applicants\Files\Delete\Controller::class);
});

$map->attach('applicants.', '/applicants', function ($r) {
    $r->get('update', '/{applicant_id}/update', Web\Applicants\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{applicant_id}/delete', Web\Applicants\Delete\Controller::class);
    $r->get('view',   '/{applicant_id}'       , Web\Applicants\Info\Controller::class);
    $r->get('apply',  '/apply' , Web\Applicants\Apply\Controller::class)->allows(['POST']);;
    $r->get('index',  ''       , Web\Applicants\List\Controller::class);
});

$map->attach('applications.', '/applications', function ($r) {
    $r->get('archive',   '/{application_id}/archive'  , Web\Applications\Archive\Controller::class);
    $r->get('unarchive', '/{application_id}/unarchive', Web\Applications\Unarchive\Controller::class);
    $r->get('delete',    '/{application_id}/delete'   , Web\Applications\Delete\Controller::class);
    $r->get('report',    '/report'   , Web\Applications\Report\Controller::class)->allows(['POST']);
});

$map->attach('appointers.', '/appointers', function ($r) {
    $r->get('update', '/{appointer_id}/update', Web\Appointers\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add', Web\Appointers\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Appointers\List\Controller::class);
});

$map->attach('committees.', '/committees', function ($r) {
    $r->get('members',      '/{committee_id}/members'      , Web\Committees\Members\Controller::class);
    $r->get('update',       '/{committee_id}/update'       , Web\Committees\Update\Controller::class)->allows(['POST']);
    $r->get('end',          '/{committee_id}/end'          , Web\Committees\End\Controller::class)->allows(['POST']);
    $r->get('seats',        '/{committee_id}/seats'        , Web\Committees\Seats\Controller::class);
    $r->get('statutes',     '/{committee_id}/statutes'     , Web\Committees\Statutes\Info\Controller::class);
    $r->get('liaisons',     '/{committee_id}/liaisons'     , Web\Committees\Liaisons\Info\Controller::class);
    $r->get('applications', '/{committee_id}/applications' , Web\Committees\Applications\Controller::class);
    $r->get('meetings',     '/{committee_id}/meetings'     , Web\Meetings\List\Controller::class);
    $r->get('meetingsync',  '/{committee_id}/meetings/sync', Web\Committees\Meetings\Sync\Controller::class);
    $r->get('history',      '/{committee_id}/history'      , Web\Committees\History\Controller::class);
    $r->get('info',         '/{committee_id}'              , Web\Committees\Info\Controller::class);
    $r->get('add',          '/add'               , Web\Committees\Add\Controller::class)->allows(['POST']);
    $r->get('report',       '/report'            , Web\Committees\Report\Controller::class);
    $r->get('index',        ''                   , Web\Committees\List\Controller::class);
});

$map->attach('committeeStatutes.', '/committeeStatutes', function ($r) {
    $r->get('add',    '/add', Web\Committees\Statutes\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{committeeStatute_id}/update', Web\Committees\Statutes\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{committeeStatute_id}/delete', Web\Committees\Statutes\Delete\Controller::class);
});

$map->attach('departments.', '/departments', function ($r) {
    $r->get('info',   '/{department_id}/info'  , Web\Departments\Info\Controller::class);
    $r->get('update', '/{department_id}/update', Web\Departments\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add', Web\Departments\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Departments\List\Controller::class);
});

$map->attach('legislationActions.', '/legislationActions', function ($r) {
    $r->get('add',    '/add'        , Web\Legislation\Actions\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{id}/update', Web\Legislation\Actions\Update\Controller::class)->allows(['POST']);
});

$map->attach('legislationActionTypes.', '/legislationActionTypes', function ($r) {
    $r->get('update', '/{id}/update', Web\Legislation\ActionTypes\Update\Controller::class)->allows(['POST']);
    $r->get('add'   , '/add'        , Web\Legislation\ActionTypes\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''            , Web\Legislation\ActionTypes\List\Controller::class);
});

$map->attach('legislation.', '/committees/{committee_id}/legislation', function ($r) {
    $r->get('add',    '/add'         , Web\Legislation\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{id}/update' , Web\Legislation\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{id}/delete' , Web\Legislation\Delete\Controller::class);
    $r->get('view',   '/{id}'        , Web\Legislation\Info\Controller::class);
    $r->get('years',  '/years'       , Web\Legislation\Years\Controller::class);
    $r->get('index',  ''             , Web\Legislation\Find\Controller::class);
});

$map->attach('legislationFiles.', '/legislationFiles', function ($r) {
    $r->get('add'   ,   '/add'        , Web\Legislation\Files\Add\Controller::class)->allows(['POST']);
    $r->get('update',   '/{id}/update', Web\Legislation\Files\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{id}/delete', Web\Legislation\Files\Delete\Controller::class);
    $r->get('download', '/{id}'       , Web\Legislation\Files\Download\Controller::class);
});

$map->attach('legislationStatuses.', '/legislationStatuses', function ($r) {
    $r->get('update', '/{id}/update', Web\Legislation\Statuses\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{id}/delete', Web\Legislation\Statuses\Delete\Controller::class);
    $r->get('add'   , '/add'        , Web\Legislation\Statuses\Add\Controller::class)->allows(['POST']);
    $r->get('index' , ''            , Web\Legislation\Statuses\List\Controller::class);
});

$map->attach('legislationTypes.', '/legislationTypes', function ($r) {
    $r->get('update', '/{id}/update', Web\Legislation\Types\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add'        , Web\Legislation\Types\Add\Controller::class)->allows(['POST']);
    $r->get('index',  '',        Web\Legislation\Types\List\Controller::class);
});

$map->attach('liaisons.', '/liaisons', function ($r) {
    $r->get('add', '/add'           , Web\Liaisons\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{id}/update', Web\Liaisons\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{id}/delete', Web\Liaisons\Delete\Controller::class);
    $r->get('index',  ''            , Web\Liaisons\List\Controller::class);
});

$map->attach('meetings.', '/meetings', function ($r) {
    $r->get('attendance', '/{id}/attendance', Web\Meetings\Attendance\Controller::class)->allows(['POST']);
    $r->get('view',       '/{id}',            Web\Meetings\Info\Controller::class);
    $r->get('index',      '',                 Web\Meetings\List\Controller::class);
});

$map->attach('meetingFiles.', '/meetingFiles', function ($r) {
    $r->get('download', '/{id}/download', Web\MeetingFiles\Download\Controller::class);
    $r->get('update',   '/{id}/update'  , Web\MeetingFiles\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{id}/delete'  , Web\MeetingFiles\Delete\Controller::class);
    $r->get('add',      '/add'          , Web\MeetingFiles\Add\Controller::class)->allows(['POST']);
    $r->get('years',    '/years'        , Web\MeetingFiles\Years\Controller::class);
    $r->get('index',    ''              , Web\MeetingFiles\List\Controller::class);
});

$map->attach('members.', '/members', function ($r) {
    $r->get('appoint',   '/appoint'  , Web\Members\Appoint\Controller::class  )->allows(['POST']);
    $r->get('reappoint', '/{id}/reappoint', Web\Members\Reappoint\Controller::class)->allows(['POST']);
    $r->get('update',    '/{id}/update'   , Web\Members\Update\Controller::class   )->allows(['POST']);
    $r->get('resign',    '/{id}/resign'   , Web\Members\Resign\Controller::class   )->allows(['POST']);
    $r->get('delete',    '/{id}/delete'   , Web\Members\Delete\Controller::class);
    $r->get('view',      '/{id}'          , Web\Members\Info\Controller::class);
});

$map->attach('offices.', '/offices', function ($r) {
    $r->get('update', '/{id}/update', Web\Offices\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add'        , Web\Offices\Add\Controller::class)->allows(['POST']);
});

$map->attach('people.', '/people', function ($r) {
    $r->get('update',     '/{id}/update', Web\People\Update\Controller::class)->allows(['POST']);
    $r->get('delete',     '/{id}/delete', Web\People\Delete\Controller::class);
    $r->get('view',       '/{id}'       , Web\People\View\Controller::class);
    $r->get('add',        '/add'        , Web\People\Add\Controller::class)->allows(['POST']);
    $r->get('callback',   '/callback'   , Web\People\Callback\Controller::class);
    $r->get('index',      ''            , Web\People\Find\Controller::class);
});

$map->attach('races.', '/races', function ($r) {
    $r->get('update', '/{id}/update', Web\Races\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add'        , Web\Races\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''            , Web\Races\List\Controller::class);
});

$map->attach('reports.', '/reports', function ($r) {
    $r->get('download', '/{id}/download', Web\Reports\Download\Controller::class);
    $r->get('update',   '/{id}/update'  , Web\Reports\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{id}/delete'  , Web\Reports\Delete\Controller::class);
    $r->get('add',      '/add'          , Web\Reports\Add\Controller::class)->allows(['POST']);
    $r->get('index',    ''              , Web\Reports\List\Controller::class);
});

$map->attach('seats.', '/seats', function ($r) {
    $r->get('update',    '/{id}/update'   , Web\Seats\Update\Controller::class)->allows(['POST']);
    $r->get('delete',    '/{id}/delete'   , Web\Seats\Delete\Controller::class);
    $r->get('end',       '/{id}/end'      , Web\Seats\End\Controller::class)->allows(['POST']);
    $r->get('view',      '/{id}'          , Web\Seats\View\Controller::class);
    $r->get('add',       '/add'           , Web\Seats\Add\Controller::class)->allows(['POST']);
    $r->get('vacancies', '/vacancies'     , Web\Seats\Vacancies\Controller::class);
    $r->get('index',     ''               , Web\Seats\List\Controller::class);
});

$map->attach('settings.', '/settings', function ($r) {
    $r->get('index',     ''          , Web\Settings\Index\Controller::class);
});

$map->attach('site.', '/site', function ($r) {
    $r->get('updateContent', '/updateContent', Web\Site\Update\Controller::class)->allows(['POST']);
    $r->get('index',  ''                     , Web\Site\Content\Controller::class);
});

$map->attach('terms.', '/terms', function ($r) {
    $r->get('update',   '/{id}/update'  , Web\Terms\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{id}/delete'  , Web\Terms\Delete\Controller::class);
    $r->get('generate', '/{id}/generate', Web\Terms\Generate\Controller::class);
    $r->get('add',      '/add'          , Web\Terms\Add\Controller::class)->allows(['POST']);
});

$map->attach('users.', '/users', function ($r) {
    $r->get('update', '/{id}/update', Web\Users\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{id}/delete', Web\Users\Delete\Controller::class);
    $r->get('add',    '/add'        , Web\Users\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''            , Web\Users\Find\Controller::class);
});
