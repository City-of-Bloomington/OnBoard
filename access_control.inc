<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

$ZEND_ACL = new Acl();
$ZEND_ACL->addRole(new Role('Anonymous'))
		 ->addRole(new Role('Public'),               'Anonymous')
		 ->addRole(new Role('Applicants-view-only'), 'Public')
		 ->addRole(new Role('Reviewer'),             'Public')
		 ->addRole(new Role('meetingFileUploader'),  'Public')
		 ->addRole(new Role('Staff'),                'Reviewer')
		 ->addRole(new Role('Administrator'));

/**
 * Declare all the controllers as resources
 *
 * These need to match what's going to be in the URL.
 * The resource should be the name of the controller, lowercased,
 * and with the word "Controller" stripped off.
 */
$ZEND_ACL->addResource(new Resource('index'));
$ZEND_ACL->addResource(new Resource('callback'));
$ZEND_ACL->addResource(new Resource('login'));
$ZEND_ACL->addResource(new Resource('departments'));
$ZEND_ACL->addResource(new Resource('people'));
$ZEND_ACL->addResource(new Resource('users'));
$ZEND_ACL->addResource(new Resource('site'));

$ZEND_ACL->addResource(new Resource('appointers'));
$ZEND_ACL->addResource(new Resource('committees'));
$ZEND_ACL->addResource(new Resource('offices'));
$ZEND_ACL->addResource(new Resource('races'));
$ZEND_ACL->addResource(new Resource('seats'));
$ZEND_ACL->addResource(new Resource('tags'));
$ZEND_ACL->addResource(new Resource('terms'));
$ZEND_ACL->addResource(new Resource('members'));
$ZEND_ACL->addResource(new Resource('liaisons'));
$ZEND_ACL->addResource(new Resource('applicants'));
$ZEND_ACL->addResource(new Resource('applications'));
$ZEND_ACL->addResource(new Resource('applicantFiles'));
$ZEND_ACL->addResource(new Resource('meetingFiles'));
$ZEND_ACL->addResource(new Resource('committeeStatutes'));

$ZEND_ACL->addResource(new Resource('legislation'));
$ZEND_ACL->addResource(new Resource('legislationFiles'));
$ZEND_ACL->addResource(new Resource('legislationTypes'));
$ZEND_ACL->addResource(new Resource('legislationActions'));
$ZEND_ACL->addResource(new Resource('legislationActionTypes'));

/**
 * Assign permissions to the resources
 */
// Permissions for unauthenticated browsing
$ZEND_ACL->allow(null, ['callback', 'login']);
$ZEND_ACL->allow(null, 'index', 'index');
$ZEND_ACL->allow(null, ['people', 'legislation', 'liaisons'], ['index','view']);
$ZEND_ACL->allow(null, 'committees', ['index','info', 'members', 'seats', 'report', 'meetings']);
$ZEND_ACL->allow(null, 'seats', ['index','view', 'vacancies']);
$ZEND_ACL->allow(null, 'applicants', 'apply');
$ZEND_ACL->allow(null, 'meetingFiles', ['index', 'download', 'years']);


// Allow Staff to do stuff
$ZEND_ACL->allow('Applicants-view-only', 'committees',   'applications');
$ZEND_ACL->allow('Applicants-view-only', 'applicantFiles', 'download');
$ZEND_ACL->allow('Applicants-view-only', 'applicants',   ['index', 'view']);
$ZEND_ACL->allow('Applicants-view-only', 'applications', ['index', 'report']);
$ZEND_ACL->allow('Applicants-view-only', 'people',       'viewContactInfo');

$ZEND_ACL->allow('Reviewer', 'committees', 'applications');
$ZEND_ACL->allow('Reviewer', 'applicantFiles');
$ZEND_ACL->allow('Reviewer', 'applicants',   ['index', 'view', 'update']);
$ZEND_ACL->allow('Reviewer', 'applications', ['index', 'archive', 'report']);
$ZEND_ACL->allow('Reviewer', 'people', 'viewContactInfo');

$ZEND_ACL->allow('Staff', 'committees', ['edit', 'update']);
$ZEND_ACL->allow('Staff', 'people',     ['edit', 'update', 'add']);
$ZEND_ACL->allow('Staff', 'members',    ['index', 'appoint', 'reappoint', 'resign']);
$ZEND_ACL->allow('Staff', 'liaisons');

$ZEND_ACL->allow('meetingFileUploader', 'people', 'viewContactInfo');
$ZEND_ACL->allow('meetingFileUploader', 'meetingFiles');

// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
