<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$rf = new \Aura\Router\RouterFactory(BASE_URI);
$ROUTES = $rf->newInstance();

$ROUTES->add('home.index',     '/'        )->setValues(['controller' => 'Application\Controllers\HomeController'    , 'action'=>'index']);
$ROUTES->add('callback.index', '/callback')->setValues(['controller' => 'Application\Controllers\CallbackController', 'action'=>'index']);

$ROUTES->attach('login', '/login', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LoginController']);

    $r->add('login',  '/login' )->addValues(['action' => 'login' ]);
    $r->add('logout', '/logout')->addValues(['action' => 'logout']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('applicantFiles', '/applicantFiles', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\ApplicantFilesController']);

    $r->add('download', '/download')->addValues(['action' => 'download']);
    $r->add('delete',   '/delete'  )->addValues(['action' => 'delete'  ]);
});

$ROUTES->attach('applicants', '/applicants', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\ApplicantsController']);

    $r->add('view',   '/view'  )->addValues(['action' => 'view'  ]);
    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
    $r->add('apply',  '/apply' )->addValues(['action' => 'apply' ]);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('applications', '/applications', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\ApplicationsController']);

    $r->add('archive',   '/archive'  )->addValues(['action' => 'archive'  ]);
    $r->add('unarchive', '/unarchive')->addValues(['action' => 'unarchive']);
    $r->add('report',    '/report'   )->addValues(['action' => 'report'   ]);
    $r->add('delete',    '/delete'   )->addValues(['action' => 'delete'   ]);
});

$ROUTES->attach('appointers', '/appointers', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\AppointersController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('committees', '/committees', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\CommitteesController']);

    $r->add('info',         '/info'        )->addValues(['action' => 'info'        ]);
    $r->add('report',       '/report'      )->addValues(['action' => 'report'      ]);
    $r->add('members',      '/members'     )->addValues(['action' => 'members'     ]);
    $r->add('update',       '/update'      )->addValues(['action' => 'update'      ]);
    $r->add('end',          '/end'         )->addValues(['action' => 'end'         ]);
    $r->add('seats',        '/seats'       )->addValues(['action' => 'seats'       ]);
    $r->add('applications', '/applications')->addValues(['action' => 'applications']);
    $r->add('meetings',     '/meetings'    )->addValues(['action' => 'meetings'    ]);
    $r->add('history',      '/history'     )->addValues(['action' => 'history'     ]);
    $r->add('index',        ''             )->addValues(['action' => 'index'       ]);
});

$ROUTES->attach('committeeStatutes', '/committeeStatutes', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\CommitteeStatutesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
});

$ROUTES->attach('departments', '/departments', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\DepartmentsController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('legislationActions', '/legislationActions', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationActionsController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
});

$ROUTES->attach('legislationActionTypes', '/legislationActionTypes', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationActionTypesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('legislation', '/legislation', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationController']);

    $r->add('view',   '/view'  )->addValues(['action' => 'view'  ]);
    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
    $r->add('years',  '/years' )->addValues(['action' => 'years' ]);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('legislationFiles', '/legislationFiles', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationFilesController']);

    $r->add('update',   '/update'  )->addValues(['action' => 'update'  ]);
    $r->add('delete',   '/delete'  )->addValues(['action' => 'delete'  ]);
    $r->add('download', '/download')->addValues(['action' => 'download']);
});

$ROUTES->attach('legislationStatuses', '/legislationStatuses', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationStatusesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('legislationTypes', '/legislationTypes', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LegislationTypesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('liaisons', '/liaisons', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\LiaisonsController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('meetingFiles', '/meetingFiles', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\MeetingFilesController']);

    $r->add('download', '/download')->addValues(['action' => 'download']);
    $r->add('update',   '/update'  )->addValues(['action' => 'update'  ]);
    $r->add('delete',   '/delete'  )->addValues(['action' => 'delete'  ]);
    $r->add('years',    '/years'   )->addValues(['action' => 'years'   ]);
    $r->add('index',    ''         )->addValues(['action' => 'index'   ]);
});

$ROUTES->attach('members', '/members', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\MembersController']);

    $r->add('appoint',   '/appoint'  )->addValues(['action' => 'appoint'  ]);
    $r->add('reappoint', '/reappoint')->addValues(['action' => 'reappoint']);
    $r->add('update',    '/update'   )->addValues(['action' => 'update'   ]);
    $r->add('delete',    '/delete'   )->addValues(['action' => 'delete'   ]);
    $r->add('resign',    '/resign'   )->addValues(['action' => 'resign'   ]);
});

$ROUTES->attach('offices', '/offices', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\OfficesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
});

$ROUTES->attach('people', '/people', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\PeopleController']);

    $r->add('view',       '/view'      )->addValues(['action' => 'view'      ]);
    $r->add('update',     '/update'    )->addValues(['action' => 'update'    ]);
    $r->add('delete',     '/delete'    )->addValues(['action' => 'delete'    ]);
    $r->add('parameters', '/parameters')->addValues(['action' => 'parameters']);
    $r->add('index',      ''           )->addValues(['action' => 'index'     ]);
});

$ROUTES->attach('races', '/races', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\RacesController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('reports', '/reports', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\ReportsController']);

    $r->add('download', '/download')->addValues(['action' => 'download']);
    $r->add('update',   '/update'  )->addValues(['action' => 'update'  ]);
    $r->add('delete',   '/delete'  )->addValues(['action' => 'delete'  ]);
    $r->add('index',    ''         )->addValues(['action' => 'index'   ]);
});

$ROUTES->attach('seats', '/seats', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\SeatsController']);

    $r->add('vacancies', '/vacancies')->addValues(['action' => 'vacancies']);
    $r->add('view',      '/view'     )->addValues(['action' => 'view'     ]);
    $r->add('update',    '/update'   )->addValues(['action' => 'update'   ]);
    $r->add('delete',    '/delete'   )->addValues(['action' => 'delete'   ]);
    $r->add('end',       '/end'      )->addValues(['action' => 'end'      ]);
    $r->add('index',     ''          )->addValues(['action' => 'index'    ]);
});

$ROUTES->attach('site', '/site', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\SiteController']);

    $r->add('updateContent', '/updateContent')->addValues(['action' => 'updateContent']);
    $r->add('index',  ''                     )->addValues(['action' => 'index'        ]);
});

$ROUTES->attach('tags', '/tags', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\TagsController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});

$ROUTES->attach('terms', '/terms', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\TermsController']);

    $r->add('update',   '/update'  )->addValues(['action' => 'update'  ]);
    $r->add('delete',   '/delete'  )->addValues(['action' => 'delete'  ]);
    $r->add('generate', '/generate')->addValues(['action' => 'generate']);
    $r->add('index',    ''         )->addValues(['action' => 'index'   ]);
});

$ROUTES->attach('users', '/users', function ($r) {
    $r->setValues(['controller' => 'Application\Controllers\UsersController']);

    $r->add('update', '/update')->addValues(['action' => 'update']);
    $r->add('delete', '/delete')->addValues(['action' => 'delete']);
    $r->add('index',  ''       )->addValues(['action' => 'index' ]);
});
