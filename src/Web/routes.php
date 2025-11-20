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
              'application_id'           => '\d+',
              'appointer_id'             => '\d+',
              'committee_id'             => '\d+',
              'committeeStatute_id'      => '\d+',
              'definition_id'            => '\d+',
              'department_id'            => '\d+',
              'email_id'                 => '\d+',
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
              'note_id'                  => '\d+',
              'office_id'                => '\d+',
              'person_id'                => '\d+',
              'report_id'                => '\d+',
              'seat_id'                  => '\d+',
              'subscription_id'          => '\d+',
              'term_id'                  => '\d+',
              'id'                       => '\d+']);

$map->attach('home.', '/', function ($r) {
    $r->get('calendarhook', 'google', Web\Meetings\CalendarHook\Controller::class)->allows(['POST']);
    $r->get ('index', '', Web\Committees\List\Controller::class);
});

$map->attach('login.', '/login', function ($r) {
    $r->get('logout',   '/logout'  , Web\Auth\Logout\Controller::class);
    $r->get('accessin', '/accessin', Web\Auth\AccessIndiana\Controller::class)->allows(['POST']);
    $r->get('cob',      '/cob'     , Web\Auth\Oidc\Controller::class);
    $r->get('index',    ''         , Web\Auth\Login\Controller::class);
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
    $r->get('index', '', Web\Applicants\List\Controller::class);
});

$map->attach('applications.', '/applications', function ($r) {
    $r->get('archive',   '/{application_id}/archive'  , Web\Applications\Archive\Controller::class);
    $r->get('unarchive', '/{application_id}/unarchive', Web\Applications\Unarchive\Controller::class);
    $r->get('delete',    '/{application_id}/delete'   , Web\Applications\Delete\Controller::class);
    $r->get('view',      '/{application_id}'          , Web\Applications\Info\Controller::class);
    $r->get('index',     ''                           , Web\Applications\List\Controller::class);
});

$map->attach('appointers.', '/appointers', function ($r) {
    $r->get('update', '/{appointer_id}/update', Web\Appointers\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add', Web\Appointers\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Appointers\List\Controller::class);
});

$map->attach('committees.', '/committees', function ($r) {
    $r->attach('notes.', '/{committee_id}/notes', function ($r) {
        $r->get('update', '/{note_id}/update', Web\Committees\Notes\Update\Controller::class)->allows(['POST']);
        $r->get('add',    '/add',              Web\Committees\Notes\Add\Controller::class)->allows(['POST']);
    });

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
    $r->get('apply',        '/{committee_id}/apply'        , Web\Applicants\Apply\Controller::class)->allows(['POST']);
    $r->get('info',         '/{committee_id}'              , Web\Committees\Info\Controller::class);
    $r->get('add',          '/add'               , Web\Committees\Add\Controller::class)->allows(['POST']);
    $r->get('index',        ''                   , Web\Committees\List\Controller::class);
});

$map->attach('committeeStatutes.', '/committeeStatutes', function ($r) {
    $r->get('add',    '/add', Web\Committees\Statutes\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{committeeStatute_id}/update', Web\Committees\Statutes\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{committeeStatute_id}/delete', Web\Committees\Statutes\Delete\Controller::class);
});

$map->attach('departments.', '/departments', function ($r) {
    $r->get('update', '/{department_id}/update', Web\Departments\Update\Controller::class)->allows(['POST']);
    $r->get('info',   '/{department_id}'       , Web\Departments\Info\Controller::class);
    $r->get('add',    '/add', Web\Departments\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Departments\List\Controller::class);
});

$map->attach('legislationActions.', '/legislationActions', function ($r) {
    $r->get('add',    '/add', Web\Legislation\Actions\Add\Controller::class)->allows(['POST']);
    $r->get('update', '/{legislationAction_id}/update', Web\Legislation\Actions\Update\Controller::class)->allows(['POST']);
});

$map->attach('legislationActionTypes.', '/legislationActionTypes', function ($r) {
    $r->get('update', '/{legislationActionType_id}/update', Web\Legislation\ActionTypes\Update\Controller::class)->allows(['POST']);
    $r->get('add'   , '/add', Web\Legislation\ActionTypes\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Legislation\ActionTypes\List\Controller::class);
});

$map->attach('legislation.', '/committees/{committee_id}/legislation', function ($r) {
    $r->get('update', '/{legislation_id}/update', Web\Legislation\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{legislation_id}/delete', Web\Legislation\Delete\Controller::class);
    $r->get('view',   '/{legislation_id}'       , Web\Legislation\Info\Controller::class);
    $r->get('add',    '/add'         , Web\Legislation\Add\Controller::class)->allows(['POST']);
    $r->get('years',  '/years'       , Web\Legislation\Years\Controller::class);
    $r->get('index',  ''             , Web\Legislation\Find\Controller::class);
});

$map->attach('legislationFiles.', '/legislationFiles', function ($r) {
    $r->get('add'   ,   '/add', Web\Legislation\Files\Add\Controller::class)->allows(['POST']);
    $r->get('update',   '/{legislationFile_id}/update', Web\Legislation\Files\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{legislationFile_id}/delete', Web\Legislation\Files\Delete\Controller::class);
    $r->get('download', '/{legislationFile_id}'       , Web\Legislation\Files\Download\Controller::class);
});

$map->attach('legislationStatuses.', '/legislationStatuses', function ($r) {
    $r->get('update', '/{legislationStatus_id}/update', Web\Legislation\Statuses\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{legislationStatus_id}/delete', Web\Legislation\Statuses\Delete\Controller::class);
    $r->get('add'   , '/add', Web\Legislation\Statuses\Add\Controller::class)->allows(['POST']);
    $r->get('index' , ''    , Web\Legislation\Statuses\List\Controller::class);
});

$map->attach('legislationTypes.', '/legislationTypes', function ($r) {
    $r->get('update', '/{legislationType_id}/update', Web\Legislation\Types\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add', Web\Legislation\Types\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Legislation\Types\List\Controller::class);
});

$map->attach('liaisons.', '/liaisons', function ($r) {
    $r->get('update', '/{liaison_id}/update', Web\Liaisons\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{liaison_id}/delete', Web\Liaisons\Delete\Controller::class);
    $r->get('add',    '/add', Web\Liaisons\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Liaisons\List\Controller::class);
});

$map->attach('meetings.', '/meetings', function ($r) {
    $r->get('attendance', '/{meeting_id}/attendance', Web\Meetings\Attendance\Controller::class)->allows(['POST']);
    $r->get('update',     '/{meeting_id}/update',     Web\Meetings\Update\Controller::class)->allows(['POST']);
    $r->get('delete',     '/{meeting_id}/delete',     Web\Meetings\Delete\Controller::class);
    $r->get('view',       '/{meeting_id}',            Web\Meetings\Info\Controller::class);
    $r->get('index',      '', Web\Meetings\List\Controller::class);
});

$map->attach('meetingFiles.', '/meetingFiles', function ($r) {
    $r->get('download', '/{meetingFile_id}/download', Web\MeetingFiles\Download\Controller::class);
    $r->get('update',   '/{meetingFile_id}/update'  , Web\MeetingFiles\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{meetingFile_id}/delete'  , Web\MeetingFiles\Delete\Controller::class);
    $r->get('add',      '/add'  , Web\MeetingFiles\Add\Controller::class)->allows(['POST']);
    $r->get('years',    '/years', Web\MeetingFiles\Years\Controller::class);
    $r->get('index',    ''      , Web\MeetingFiles\List\Controller::class);
});

$map->attach('members.', '/members', function ($r) {
    $r->get('appoint',   '/appoint'  , Web\Members\Appoint\Controller::class  )->allows(['POST']);
    $r->get('reappoint', '/{member_id}/reappoint', Web\Members\Reappoint\Controller::class)->allows(['POST']);
    $r->get('update',    '/{member_id}/update'   , Web\Members\Update\Controller::class   )->allows(['POST']);
    $r->get('resign',    '/{member_id}/resign'   , Web\Members\Resign\Controller::class   )->allows(['POST']);
    $r->get('delete',    '/{member_id}/delete'   , Web\Members\Delete\Controller::class);
    $r->get('view',      '/{member_id}'          , Web\Members\Info\Controller::class);
});

$map->attach('notifications.', '/notifications', function ($r) {
    $r->attach('definitions.', '/definitions', function ($r) {
        $r->get('update', '/{definition_id}/update', Web\Notifications\Definitions\Update\Controller::class)->allows(['POST']);
        $r->get('delete', '/{definition_id}/delete', Web\Notifications\Definitions\Delete\Controller::class);
        $r->get('info',   '/{definition_id}',        Web\Notifications\Definitions\Info\Controller::class);
        $r->get('add',    '/add',                   Web\Notifications\Definitions\Add\Controller::class)->allows(['POST']);
    });
    $r->attach('log.', '/log', function ($r) {
        $r->get('info', '/{email_id}', Web\Notifications\Email\Info\Controller::class);
    });
    $r->get('index', '', Web\Notifications\Index\Controller::class);
});

$map->attach('offices.', '/offices', function ($r) {
    $r->get('update', '/{office_id}/update', Web\Offices\Update\Controller::class)->allows(['POST']);
    $r->get('add',    '/add', Web\Offices\Add\Controller::class)->allows(['POST']);
});

$map->attach('people.', '/people', function ($r) {
    $r->attach('merge.', '/merge', function ($r) {
        $r->get('index',   '', Web\People\Merge\Candidates\Controller::class)->allows(['POST']);
    });
    $r->get('update',     '/{person_id}/update', Web\People\Update\Controller::class)->allows(['POST']);
    $r->get('delete',     '/{person_id}/delete', Web\People\Delete\Controller::class);
    $r->get('view',       '/{person_id}'       , Web\People\View\Controller::class);
    $r->get('add',        '/add'     , Web\People\Add\Controller::class)->allows(['POST']);
    $r->get('callback',   '/callback', Web\People\Callback\Controller::class);
    $r->get('index',      ''         , Web\People\Find\Controller::class);
});
$map->attach('emails.', '/people/{person_id}/emails', function ($r) {
    $r->get('update', '/{email_id}/update', Web\People\Emails\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{email_id}/delete', Web\People\Emails\Delete\Controller::class);
    $r->get('add',    '/add',               Web\People\Emails\Add\Controller::class)->allows(['POST']);
});
$map->attach('phones.', '/people/{person_id}/phones', function ($r) {
    $r->get('update', '/{phone_id}/update', Web\People\Phones\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{phone_id}/delete', Web\People\Phones\Delete\Controller::class);
    $r->get('add',    '/add',               Web\People\Phones\Add\Controller::class)->allows(['POST']);
});

$map->attach('profile.', '/profile', function ($r) {
    $r->attach('emails.', '/emails', function ($r) {
        $r->get('delete',  '/{email_id}/delete', Web\Profile\Emails\Delete\Controller::class);
        $r->get('update',  '/{email_id}/update', Web\Profile\Emails\Update\Controller::class)->allows(['POST']);
        $r->get('add',     '/add',               Web\Profile\Emails\Add\Controller::class)->allows(['POST']);
    });
    $r->attach('phones.', '/phones', function ($r) {
        $r->get('delete',  '/{phone_id}/delete', Web\Profile\Phones\Delete\Controller::class);
        $r->get('update',  '/{phone_id}/update', Web\Profile\Phones\Update\Controller::class)->allows(['POST']);
        $r->get('add',     '/add',               Web\Profile\Phones\Add\Controller::class)->allows(['POST']);
    });
    $r->attach('files.', '/files', function ($r) {
        $r->get('download', '/{applicantFile_id}/download', Web\Profile\Files\Download\Controller::class);
        $r->get('delete',   '/{applicantFile_id}/delete',   Web\Profile\Files\Delete\Controller::class);
        $r->get('update',   '/{applicantFile_id}/update',   Web\Profile\Files\Update\Controller::class);
        $r->get('add',      '/add',                         Web\Profile\Files\Add\Controller::class)->allows(['POST']);
    });
    $r->attach('notifications.', '/notifications', function ($r) {
        $r->get('delete', '/{subscription_id}/delete', Web\Profile\Notifications\Delete\Controller::class);
        $r->get('add',    '/add',                      Web\Profile\Notifications\Add\Controller::class)->allows(['POST']);
    });
    $r->get('update', '/update', Web\Profile\Update\Controller::class)->allows(['POST']);
    $r->get('index',  '',        Web\Profile\Info\Controller::class);
});

$map->attach('reports.', '/reports', function ($r) {
    $r->get('download', '/{report_id}/download', Web\Reports\Download\Controller::class);
    $r->get('update',   '/{report_id}/update'  , Web\Reports\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{report_id}/delete'  , Web\Reports\Delete\Controller::class);
    $r->get('add',      '/add'          , Web\Reports\Add\Controller::class)->allows(['POST']);
    $r->get('index',    ''              , Web\Reports\List\Controller::class);
});

$map->attach('seats.', '/seats', function ($r) {
    $r->get('update',    '/{seat_id}/update'   , Web\Seats\Update\Controller::class)->allows(['POST']);
    $r->get('delete',    '/{seat_id}/delete'   , Web\Seats\Delete\Controller::class);
    $r->get('end',       '/{seat_id}/end'      , Web\Seats\End\Controller::class)->allows(['POST']);
    $r->get('view',      '/{seat_id}'          , Web\Seats\View\Controller::class);
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
    $r->get('update',   '/{term_id}/update'  , Web\Terms\Update\Controller::class)->allows(['POST']);
    $r->get('delete',   '/{term_id}/delete'  , Web\Terms\Delete\Controller::class);
    $r->get('generate', '/{term_id}/generate', Web\Terms\Generate\Controller::class);
    $r->get('add',      '/add', Web\Terms\Add\Controller::class)->allows(['POST']);
});

$map->attach('users.', '/users', function ($r) {
    $r->get('update', '/{person_id}/update', Web\Users\Update\Controller::class)->allows(['POST']);
    $r->get('delete', '/{person_id}/delete', Web\Users\Delete\Controller::class);
    $r->get('add',    '/add', Web\Users\Add\Controller::class)->allows(['POST']);
    $r->get('index',  ''    , Web\Users\Find\Controller::class);
});
