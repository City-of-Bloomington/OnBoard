<?php
/**
 * @copyright 2020-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$ROUTES = new \Aura\Router\RouterContainer(BASE_URI);
$map    = $ROUTES->getMap();

$map->get('home.index',     '/',         'Application\Controllers\HomeController'    )->extras(['action'=>'index']);
$map->get('callback.index', '/callback', 'Application\Controllers\CallbackController')->extras(['action'=>'index']);

$map->attach('login.', '/login', function ($r) {
    $r->get('login',  '/login' , 'Application\Controllers\LoginController')->extras(['action' => 'login' ]);
    $r->get('logout', '/logout', 'Application\Controllers\LoginController')->extras(['action' => 'logout']);
    $r->get('index',  ''       , 'Application\Controllers\LoginController')->extras(['action' => 'index' ]);
});

$map->attach('alternates.', '/alternates', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\AlternatesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\AlternatesController')->extras(['action' => 'delete']);
});

$map->attach('applicantFiles.', '/applicantFiles', function ($r) {
    $r->get('download', '/download', 'Application\Controllers\ApplicantFilesController')->extras(['action' => 'download']);
    $r->get('delete',   '/delete'  , 'Application\Controllers\ApplicantFilesController')->extras(['action' => 'delete'  ]);
});

$map->attach('applicants.', '/applicants', function ($r) {
    $r->get('view',   '/view'  , 'Application\Controllers\ApplicantsController')->extras(['action' => 'view'  ]);
    $r->get('update', '/update', 'Application\Controllers\ApplicantsController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\ApplicantsController')->extras(['action' => 'delete']);
    $r->get('apply',  '/apply' , 'Application\Controllers\ApplicantsController')->extras(['action' => 'apply' ])->allows(['POST']);;
    $r->get('index',  ''       , 'Application\Controllers\ApplicantsController')->extras(['action' => 'index' ]);
});

$map->attach('applications.', '/applications', function ($r) {
    $r->get('archive',   '/archive'  , 'Application\Controllers\ApplicationsController')->extras(['action' => 'archive'  ]);
    $r->get('unarchive', '/unarchive', 'Application\Controllers\ApplicationsController')->extras(['action' => 'unarchive']);
    $r->get('report',    '/report'   , 'Application\Controllers\ApplicationsController')->extras(['action' => 'report'   ]);
    $r->get('delete',    '/delete'   , 'Application\Controllers\ApplicationsController')->extras(['action' => 'delete'   ]);
});

$map->attach('appointers.', '/appointers', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\AppointersController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('index',  ''       , 'Application\Controllers\AppointersController')->extras(['action' => 'index' ]);
});

$map->attach('committees.', '/committees', function ($r) {
    $r->get('info',         '/info'        , 'Application\Controllers\CommitteesController')->extras(['action' => 'info'        ]);
    $r->get('report',       '/report'      , 'Application\Controllers\CommitteesController')->extras(['action' => 'report'      ]);
    $r->get('members',      '/members'     , 'Application\Controllers\CommitteesController')->extras(['action' => 'members'     ]);
    $r->get('update',       '/update'      , 'Application\Controllers\CommitteesController')->extras(['action' => 'update'      ])->allows(['POST']);
    $r->get('end',          '/end'         , 'Application\Controllers\CommitteesController')->extras(['action' => 'end'         ]);
    $r->get('seats',        '/seats'       , 'Application\Controllers\CommitteesController')->extras(['action' => 'seats'       ]);
    $r->get('applications', '/applications', 'Application\Controllers\CommitteesController')->extras(['action' => 'applications']);
    $r->get('meetings',     '/meetings'    , 'Application\Controllers\CommitteesController')->extras(['action' => 'meetings'    ]);
    $r->get('history',      '/history'     , 'Application\Controllers\CommitteesController')->extras(['action' => 'history'     ]);
    $r->get('index',        ''             , 'Application\Controllers\CommitteesController')->extras(['action' => 'index'       ]);
});

$map->attach('committeeStatutes.', '/committeeStatutes', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\CommitteeStatutesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\CommitteeStatutesController')->extras(['action' => 'delete']);
});

$map->attach('departments.', '/departments', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\DepartmentsController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('view',   '/view',   'Application\Controllers\DepartmentsController')->extras(['action' => 'view'  ]);
    $r->get('index',  ''       , 'Application\Controllers\DepartmentsController')->extras(['action' => 'index' ]);
});

$map->attach('legislationActions.', '/legislationActions', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\LegislationActionsController')->extras(['action' => 'update'])->allows(['POST']);
});

$map->attach('legislationActionTypes.', '/legislationActionTypes', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\LegislationActionTypesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('index',  ''       , 'Application\Controllers\LegislationActionTypesController')->extras(['action' => 'index' ]);
});

$map->attach('legislation.', '/legislation', function ($r) {
    $r->get('view',   '/view'  , 'Application\Controllers\LegislationController')->extras(['action' => 'view'  ]);
    $r->get('update', '/update', 'Application\Controllers\LegislationController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\LegislationController')->extras(['action' => 'delete']);
    $r->get('years',  '/years' , 'Application\Controllers\LegislationController')->extras(['action' => 'years' ]);
    $r->get('index',  ''       , 'Application\Controllers\LegislationController')->extras(['action' => 'index' ]);
});

$map->attach('legislationFiles.', '/legislationFiles', function ($r) {
    $r->get('update',   '/update'  , 'Application\Controllers\LegislationFilesController')->extras(['action' => 'update'  ])->allows(['POST']);
    $r->get('delete',   '/delete'  , 'Application\Controllers\LegislationFilesController')->extras(['action' => 'delete'  ]);
    $r->get('download', '/download', 'Application\Controllers\LegislationFilesController')->extras(['action' => 'download']);
});

$map->attach('legislationStatuses.', '/legislationStatuses', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\LegislationStatusesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\LegislationStatusesController')->extras(['action' => 'delete']);
    $r->get('index',  ''       , 'Application\Controllers\LegislationStatusesController')->extras(['action' => 'index' ]);
});

$map->attach('legislationTypes.', '/legislationTypes', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\LegislationTypesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('index',  ''       , 'Application\Controllers\LegislationTypesController')->extras(['action' => 'index' ]);
});

$map->attach('liaisons.', '/liaisons', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\LiaisonsController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\LiaisonsController')->extras(['action' => 'delete']);
    $r->get('index',  ''       , 'Application\Controllers\LiaisonsController')->extras(['action' => 'index' ]);
});

$map->attach('meetingFiles.', '/meetingFiles', function ($r) {
    $r->get('download', '/download', 'Application\Controllers\MeetingFilesController')->extras(['action' => 'download']);
    $r->get('update',   '/update'  , 'Application\Controllers\MeetingFilesController')->extras(['action' => 'update'  ])->allows(['POST']);
    $r->get('delete',   '/delete'  , 'Application\Controllers\MeetingFilesController')->extras(['action' => 'delete'  ]);
    $r->get('years',    '/years'   , 'Application\Controllers\MeetingFilesController')->extras(['action' => 'years'   ]);
    $r->get('index',    ''         , 'Application\Controllers\MeetingFilesController')->extras(['action' => 'index'   ]);
});

$map->attach('members.', '/members', function ($r) {
    $r->get('appoint',   '/appoint'  , 'Application\Controllers\MembersController')->extras(['action' => 'appoint'  ])->allows(['POST']);
    $r->get('reappoint', '/reappoint', 'Application\Controllers\MembersController')->extras(['action' => 'reappoint'])->allows(['POST']);
    $r->get('update',    '/update'   , 'Application\Controllers\MembersController')->extras(['action' => 'update'   ])->allows(['POST']);
    $r->get('delete',    '/delete'   , 'Application\Controllers\MembersController')->extras(['action' => 'delete'   ]);
    $r->get('resign',    '/resign'   , 'Application\Controllers\MembersController')->extras(['action' => 'resign'   ])->allows(['POST']);
});

$map->attach('offices.', '/offices', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\OfficesController')->extras(['action' => 'update'])->allows(['POST']);
});

$map->attach('people.', '/people', function ($r) {
    $r->get('view',       '/view'      , 'Application\Controllers\PeopleController')->extras(['action' => 'view'      ]);
    $r->get('update',     '/update'    , 'Application\Controllers\PeopleController')->extras(['action' => 'update'    ])->allows(['POST']);
    $r->get('delete',     '/delete'    , 'Application\Controllers\PeopleController')->extras(['action' => 'delete'    ]);
    $r->get('parameters', '/parameters', 'Application\Controllers\PeopleController')->extras(['action' => 'parameters']);
    $r->get('index',      ''           , Web\People\Find\Controller::class);
});

$map->attach('races.', '/races', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\RacesController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('index',  ''       , 'Application\Controllers\RacesController')->extras(['action' => 'index' ]);
});

$map->attach('reports.', '/reports', function ($r) {
    $r->get('download', '/download', 'Application\Controllers\ReportsController')->extras(['action' => 'download']);
    $r->get('update',   '/update'  , 'Application\Controllers\ReportsController')->extras(['action' => 'update'  ])->allows(['POST']);
    $r->get('delete',   '/delete'  , 'Application\Controllers\ReportsController')->extras(['action' => 'delete'  ]);
    $r->get('index',    ''         , 'Application\Controllers\ReportsController')->extras(['action' => 'index'   ]);
});

$map->attach('seats.', '/seats', function ($r) {
    $r->get('vacancies', '/vacancies', 'Application\Controllers\SeatsController')->extras(['action' => 'vacancies']);
    $r->get('view',      '/view'     , 'Application\Controllers\SeatsController')->extras(['action' => 'view'     ]);
    $r->get('update',    '/update'   , 'Application\Controllers\SeatsController')->extras(['action' => 'update'   ])->allows(['POST']);
    $r->get('delete',    '/delete'   , 'Application\Controllers\SeatsController')->extras(['action' => 'delete'   ]);
    $r->get('end',       '/end'      , 'Application\Controllers\SeatsController')->extras(['action' => 'end'      ])->allows(['POST']);
    $r->get('index',     ''          , 'Application\Controllers\SeatsController')->extras(['action' => 'index'    ]);
});

$map->attach('site.', '/site', function ($r) {
    $r->get('updateContent', '/updateContent', 'Application\Controllers\SiteController')->extras(['action' => 'updateContent'])->allows(['POST']);
    $r->get('index',  ''                     , 'Application\Controllers\SiteController')->extras(['action' => 'index'        ]);
});

$map->attach('tags.', '/tags', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\TagsController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('index',  ''       , 'Application\Controllers\TagsController')->extras(['action' => 'index' ]);
});

$map->attach('terms.', '/terms', function ($r) {
    $r->get('update',   '/update'  , 'Application\Controllers\TermsController')->extras(['action' => 'update'  ])->allows(['POST']);
    $r->get('delete',   '/delete'  , 'Application\Controllers\TermsController')->extras(['action' => 'delete'  ]);
    $r->get('generate', '/generate', 'Application\Controllers\TermsController')->extras(['action' => 'generate']);
    $r->get('index',    ''         , 'Application\Controllers\TermsController')->extras(['action' => 'index'   ]);
});

$map->attach('users.', '/users', function ($r) {
    $r->get('update', '/update', 'Application\Controllers\UsersController')->extras(['action' => 'update'])->allows(['POST']);
    $r->get('delete', '/delete', 'Application\Controllers\UsersController')->extras(['action' => 'delete']);
    $r->get('index',  ''       , 'Application\Controllers\UsersController')->extras(['action' => 'index' ]);
});
