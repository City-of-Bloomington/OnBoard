<?php
/**
 * @copyright 2020-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$ROUTES = new \Aura\Router\RouterContainer(BASE_URI);
$map    = $ROUTES->getMap();
$map->tokens(['id' => '\d+']);

$map->get('home.index',     '/',         Web\Committees\List\Controller::class);
$map->get('callback.index', '/callback', 'Application\Controllers\CallbackController')->extras(['action'=>'index']);

$map->attach('login.', '/login', function ($r) {
    $r->get('logout', '/logout', Web\Auth\Logout\Controller::class);
    $r->get('index',  ''       , Web\Auth\Oidc\Controller::class);
});

$map->attach('alternates.', '/alternates', function ($r) {
    $r->get('update', '/update', Web\Alternates\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Alternates\Delete\Controller::class);
});

$map->attach('applicantFiles.', '/applicantFiles', function ($r) {
    $r->get('download', '/download', Web\Applicants\Files\Download\Controller::class);
    $r->get('delete',   '/delete'  , Web\Applicants\Files\Delete\Controller::class);
});

$map->attach('applicants.', '/applicants', function ($r) {
    $r->get('view',   '/view'  , Web\Applicants\Info\Controller::class);
    $r->get('update', '/update', Web\Applicants\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Applicants\Delete\Controller::class);
    $r->get('apply',  '/apply' , Web\Applicants\Apply\Controller::class)->allows(['POST']);;
    $r->get('index',  ''       , Web\Applicants\List\Controller::class);
});

$map->attach('applications.', '/applications', function ($r) {
    $r->get('archive',   '/archive'  , Web\Applications\Archive\Controller::class);
    $r->get('unarchive', '/unarchive', Web\Applications\Unarchive\Controller::class);
    $r->get('report',    '/report'   , Web\Applications\Report\Controller::class)->allows(['POST']);
    $r->get('delete',    '/delete'   , Web\Applications\Delete\Controller::class);
});

$map->attach('appointers.', '/appointers', function ($r) {
    $r->get('index',  '',        Web\Appointers\List\Controller::class);
    $r->get('update', '/update', Web\Appointers\Update\Controller::class)->allows(['POST']);
});

$map->attach('committees.', '/committees', function ($r) {
    $r->get('info',         '/{id}/info'        , Web\Committees\Info\Controller::class);
    $r->get('members',      '/{id}/members'     , Web\Committees\Members\Controller::class);
    $r->get('update',       '/update'      , Web\Committees\Update\Controller::class)->allows(['POST']);
    $r->get('end',          '/end'         , Web\Committees\End\Controller::class)->allows(['POST']);
    $r->get('seats',        '/seats'       , Web\Committees\Seats\Controller::class);
    $r->get('applications', '/applications', Web\Committees\Applications\Controller::class);
    $r->get('meetings',     '/meetings'    , Web\Committees\Meetings\Controller::class);
    $r->get('history',      '/history'     , Web\Committees\History\Controller::class);
    $r->get('report',       '/report'           , Web\Committees\Report\Controller::class);
    $r->get('index',        ''                  , Web\Committees\List\Controller::class);
});

$map->attach('committeeStatutes.', '/committeeStatutes', function ($r) {
    $r->get('update', '/update', Web\Committees\Statutes\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Committees\Statutes\Delete\Controller::class);
});

$map->attach('departments.', '/departments', function ($r) {
    $r->get('index',  ''            , Web\Departments\List\Controller::class);
    $r->get('info',   '/{id}/info'  , Web\Departments\Info\Controller::class);
    $r->get('add',    '/add'        , Web\Departments\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{id}/update', Web\Departments\Update\Controller::class)->allows(['POST']);
});

$map->attach('legislationActions.', '/legislationActions', function ($r) {
    $r->get('update', '/update', Web\Legislation\Actions\Update\Controller::class)->allows(['POST']);
});

$map->attach('legislationActionTypes.', '/legislationActionTypes', function ($r) {
    $r->get('update', '/update', Web\Legislation\ActionTypes\Update\Controller::class)->allows(['POST']);
    $r->get('index',  '',        Web\Legislation\ActionTypes\List\Controller::class);
});

$map->attach('legislation.', '/legislation', function ($r) {
    $r->get('view',   '/view'  , Web\Legislation\Info\Controller::class);
    $r->get('update', '/update', Web\Legislation\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Legislation\Delete\Controller::class);
    $r->get('years',  '/years' , Web\Legislation\Years\Controller::class);
    $r->get('index',  ''       , Web\Legislation\Find\Controller::class);
});

$map->attach('legislationFiles.', '/legislationFiles', function ($r) {
    $r->get('update',   '/update'  , Web\Legislation\Files\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/delete'  , Web\Legislation\Files\Delete\Controller::class);
    $r->get('download', '/download', Web\Legislation\Files\Download\Controller::class);
});

$map->attach('legislationStatuses.', '/legislationStatuses', function ($r) {
    $r->get('update', '/update', Web\Legislation\Statuses\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Legislation\Statuses\Delete\Controller::class);
    $r->get('index',  ''       , Web\Legislation\Statuses\List\Controller::class);
});

$map->attach('legislationTypes.', '/legislationTypes', function ($r) {
    $r->get('update', '/update', Web\Legislation\Types\Update\Controller::class)->allows(['POST']);
    $r->get('index',  '',        Web\Legislation\Types\List\Controller::class);
});

$map->attach('liaisons.', '/liaisons', function ($r) {
    $r->get('update', '/{id}/update', Web\Liaisons\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{id}/delete', Web\Liaisons\Delete\Controller::class);
    $r->get('index',  ''            , Web\Liaisons\List\Controller::class);
});

$map->attach('meetings.', '/meetings', function ($r) {
    $r->get('view', '/view', Web\Meetings\Info\Controller::class);
});

$map->attach('meetingFiles.', '/meetingFiles', function ($r) {
    $r->get('download', '/download', Web\MeetingFiles\Download\Controller::class);
    $r->get('update',   '/update'  , Web\MeetingFiles\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/delete'  , Web\MeetingFiles\Delete\Controller::class);
    $r->get('years',    '/years'   , Web\MeetingFiles\Years\Controller::class);
    $r->get('index',    ''         , Web\MeetingFiles\List\Controller::class);
});

$map->attach('members.', '/members', function ($r) {
    $r->get('appoint',   '/appoint'  , Web\Members\Appoint\Controller::class  )->allows(['POST']);
    $r->get('reappoint', '/reappoint', Web\Members\Reappoint\Controller::class)->allows(['POST']);
    $r->get('update',    '/update'   , Web\Members\Update\Controller::class   )->allows(['POST']);
    $r->get('resign',    '/resign'   , Web\Members\Resign\Controller::class   )->allows(['POST']);
    $r->get('delete',    '/delete'   , Web\Members\Delete\Controller::class);
});

$map->attach('offices.', '/offices', function ($r) {
    $r->get('update', '/update', Web\Offices\Update\Controller::class)->allows(['POST']);
});

$map->attach('people.', '/people', function ($r) {
    $r->get('view',       '/view'  , Web\People\View\Controller::class);
    $r->get('update',     '/update', Web\People\Update\Controller::class)->allows(['POST']);
    $r->get('delete',     '/delete', Web\People\Delete\Controller::class);
    $r->get('index',      ''       , Web\People\Find\Controller::class);
});

$map->attach('races.', '/races', function ($r) {
    $r->get('add',    '/add'        , Web\Races\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{id}/update', Web\Races\Update\Controller::class)->allows(['POST']);
    $r->get('index',  ''            , Web\Races\List\Controller::class);
});

$map->attach('reports.', '/reports', function ($r) {
    $r->get('download', '/download', Web\Reports\Download\Controller::class);
    $r->get('update',   '/update'  , Web\Reports\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/delete'  , Web\Reports\Delete\Controller::class);
    $r->get('index',    ''         , Web\Reports\List\Controller::class);
});

$map->attach('seats.', '/seats', function ($r) {
    $r->get('vacancies', '/vacancies', Web\Seats\Vacancies\Controller::class);
    $r->get('view',      '/view'     , Web\Seats\View\Controller::class);
    $r->get('add',       '/add'      , Web\Seats\Add\Controller::class)->allows(['POST']);
    $r->get('update',    '/update'   , Web\Seats\Update\Controller::class)->allows(['POST']);
    $r->get('delete',    '/delete'   , Web\Seats\Delete\Controller::class);
    $r->get('end',       '/end'      , Web\Seats\End\Controller::class)->allows(['POST']);
    $r->get('index',     ''          , Web\Seats\List\Controller::class);
});

$map->attach('settings.', '/settings', function ($r) {
    $r->get('index',     ''          , Web\Settings\Index\Controller::class);
});

$map->attach('site.', '/site', function ($r) {
    $r->get('updateContent', '/updateContent', Web\Site\Update\Controller::class)->allows(['POST']);
    $r->get('index',  ''                     , Web\Site\Content\Controller::class);
});

$map->attach('terms.', '/terms', function ($r) {
    $r->get('update',   '/update'  , Web\Terms\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/delete'  , Web\Terms\Delete\Controller::class);
    $r->get('generate', '/generate', Web\Terms\Generate\Controller::class);
});

$map->attach('users.', '/users', function ($r) {
    $r->get('update', '/update', Web\Users\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/delete', Web\Users\Delete\Controller::class);
    $r->get('index',  ''       , Web\Users\Find\Controller::class);
});
