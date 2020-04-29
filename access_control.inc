<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

use Application\Authorization\CommitteeAssociation;

$ACL = new Acl();
$ACL->addRole(new Role('Anonymous'))
	->addRole(new Role('Public'),               'Anonymous')
	->addRole(new Role('Appointer'),            'Public')
	->addRole(new Role('Reviewer'),             'Public')
	->addRole(new Role('meetingFileUploader'),  'Public')
    ->addRole(new Role('Liaison'), ['Appointer', 'meetingFileUploader'])
	->addRole(new Role('Staff'),                'Reviewer')
	->addRole(new Role('Administrator'));

/**
 * Declare all the controllers as resources
 *
 * These need to match what's going to be in the URL.
 * The resource should be the name of the controller, lowercased,
 * and with the word "Controller" stripped off.
 */
$ACL->addResource(new Resource('index'));
$ACL->addResource(new Resource('callback'));
$ACL->addResource(new Resource('login'));
$ACL->addResource(new Resource('departments'));
$ACL->addResource(new Resource('people'));
$ACL->addResource(new Resource('users'));
$ACL->addResource(new Resource('site'));

$ACL->addResource(new Resource('appointers'));
$ACL->addResource(new Resource('committees'));
$ACL->addResource(new Resource('offices'));
$ACL->addResource(new Resource('races'));
$ACL->addResource(new Resource('seats'));
$ACL->addResource(new Resource('tags'));
$ACL->addResource(new Resource('terms'));
$ACL->addResource(new Resource('members'));
$ACL->addResource(new Resource('liaisons'));
$ACL->addResource(new Resource('applicants'));
$ACL->addResource(new Resource('applications'));
$ACL->addResource(new Resource('applicantFiles'));
$ACL->addResource(new Resource('meetingFiles'));
$ACL->addResource(new Resource('committeeStatutes'));

$ACL->addResource(new Resource('legislation'));
$ACL->addResource(new Resource('legislationFiles'));
$ACL->addResource(new Resource('legislationTypes'));
$ACL->addResource(new Resource('legislationActions'));
$ACL->addResource(new Resource('legislationActionTypes'));
$ACL->addResource(new Resource('legislationStatuses'));

$ACL->addResource(new Resource('reports'));

/**
 * Assign permissions to the resources
 */
// Permissions for unauthenticated browsing
$ACL->allow(null, ['callback', 'login']);
$ACL->allow(null,  'index', 'index');
$ACL->allow(null, ['people', 'legislation', 'liaisons'], ['index','view', 'years']);
$ACL->allow(null,  'people', 'parameters');
$ACL->allow(null,  'committees', ['index','info', 'members', 'seats', 'report', 'meetings']);
$ACL->allow(null,  'seats', ['index','view', 'vacancies']);
$ACL->allow(null,  'applicants', 'apply');
$ACL->allow(null, ['meetingFiles', 'legislationFiles', 'reports'], ['index', 'download', 'years']);
$ACL->allow(null,  'legislationTypes', 'index');


// Allow Staff to do stuff
$ACL->allow('Appointer', 'committees',   'applications');
$ACL->allow('Appointer', 'applicantFiles', 'download');
$ACL->allow('Appointer', 'applicants',   ['index', 'view']);
$ACL->allow('Appointer', 'applications', ['index', 'report']);
$ACL->allow('Appointer', 'people',       'viewContactInfo');

$ACL->allow('Reviewer',  'committees', 'applications');
$ACL->allow('Reviewer', ['applicantFiles', 'meetingFiles']);
$ACL->allow('Reviewer', 'legislation', ['index', 'view', 'years', 'update']);
$ACL->allow('Reviewer', ['legislationFiles', 'legislationActions', 'reports']);
$ACL->allow('Reviewer',  'legislationStatuses', 'index');
$ACL->allow('Reviewer',  'applicants',   ['index', 'view', 'update']);
$ACL->allow('Reviewer',  'applications', ['index', 'archive', 'unarchive', 'report']);
$ACL->allow('Reviewer',  'people', 'viewContactInfo');

$ACL->allow('Staff', 'committees', ['update']);
$ACL->allow('Staff', 'people',     ['update']);
$ACL->allow('Staff', 'members',    ['index', 'appoint', 'reappoint', 'resign', 'update']);
$ACL->allow('Staff', 'offices',    ['update']);
$ACL->allow('Staff', 'liaisons');

$ACL->allow('meetingFileUploader',  'people', 'viewContactInfo');
$ACL->allow('meetingFileUploader',  'meetingFiles');
$ACL->allow('meetingFileUploader', ['legislation', 'legislationFiles', 'legislationActions', 'reports']);


$ACL->allow('Liaison', 'members', ['index', 'appoint', 'reappoint', 'resign', 'update'], new CommitteeAssociation());

// Administrator is allowed access to everything
$ACL->allow('Administrator');
