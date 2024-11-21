<?php
/**
 * @copyright 2014-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

use Web\Auth\CommitteeAssociation;
use Web\Auth\DepartmentAssociation;

$requiresCommitteeAssociation  = new CommitteeAssociation();
$requiresDepartmentAssociation = new DepartmentAssociation();

$ACL = new Acl();
$ACL->addRole(new Role('Anonymous'))
    ->addRole(new Role('Public'),     'Anonymous')
    ->addRole(new Role('Appointer'),  'Public')
    ->addRole(new Role('Clerk'),      'Public')
    ->addRole(new Role('Liaison'),   ['Appointer', 'Clerk'])
    ->addRole(new Role('Staff'))
    ->addRole(new Role('Administrator'));

/**
 * Create resources for all the routes
 */
foreach ($ROUTES->getMap()->getRoutes() as $r) {
    list($resource, $permission) = explode('.', $r->name);
    if (!$ACL->hasResource($resource)) {
         $ACL->addResource(new Resource($resource));
    }
}

/**
 * Assign permissions to the resources
 */
// Permissions for unauthenticated browsing
$ACL->allow(null,  'home',        'index');
$ACL->allow(null,  'people',      'parameters');
$ACL->allow(null,  'committees', ['index','info', 'members', 'seats', 'statutes', 'report', 'meetings']);
$ACL->allow(null,  'seats',      ['index','view', 'vacancies']);
$ACL->allow(null,  'applicants',  'apply');
$ACL->allow(null,  'login');
$ACL->allow(null,
            ['people', 'legislation', 'liaisons', 'meetings', 'meetingFiles', 'legislationFiles', 'reports'],
            ['index', 'view', 'years', 'download', 'callback']);

$ACL->allow('Appointer', 'committees',     'applications', $requiresDepartmentAssociation);
$ACL->allow('Appointer', 'applicantFiles', 'download',     $requiresDepartmentAssociation);
$ACL->allow('Appointer', 'applicants',     'view',         $requiresDepartmentAssociation);
$ACL->allow('Appointer', 'applications',   'report',       $requiresDepartmentAssociation);
$ACL->allow('Appointer', 'people',         'viewContactInfo');

$ACL->allow('Staff');
$ACL->deny ('Staff', 'users', ['update', 'delete']);
$ACL->deny ('Staff', 'applicantFiles', 'delete');

$ACL->allow('Clerk',  'people', 'viewContactInfo');
$ACL->allow('Clerk',
            ['meetingFiles', 'legislation', 'legislationFiles', 'legislationActions', 'reports'],
            ['add', 'update', 'delete'],
            $requiresDepartmentAssociation);

$ACL->allow('Liaison', 'committees', 'update', $requiresCommitteeAssociation);
$ACL->allow('Liaison', 'members', ['index', 'appoint', 'reappoint', 'resign', 'update'], $requiresCommitteeAssociation);
$ACL->allow('Liaison', 'offices', 'update', $requiresCommitteeAssociation);
$ACL->allow('Liaison', 'people', 'update');

// Administrator is allowed access to everything
$ACL->allow('Administrator');
