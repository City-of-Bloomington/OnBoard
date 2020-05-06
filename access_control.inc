<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

use Web\Auth\CommitteeAssociation;

$requiresCommitteeAssociation = new CommitteeAssociation();

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
 * Create resources for all the routes
 */
foreach ($ROUTES->getRoutes() as $r) {
    list($resource, $permission) = explode('.', $r->name);
    if (!$ACL->hasResource($resource)) {
         $ACL->addResource(new Resource($resource));
    }
}

/**
 * Assign permissions to the resources
 */
// Permissions for unauthenticated browsing
$ACL->allow(null, ['callback', 'login']);
$ACL->allow(null,  'home', 'index');
$ACL->allow(null, ['people', 'legislation', 'liaisons'], ['index','view', 'years']);
$ACL->allow(null,  'people', 'parameters');
$ACL->allow(null,  'committees', ['index','info', 'members', 'seats', 'report', 'meetings']);
$ACL->allow(null,  'seats', ['index','view', 'vacancies']);
$ACL->allow(null,  'applicants', 'apply');
$ACL->allow(null, ['meetingFiles', 'legislationFiles', 'reports'], ['index', 'download', 'years']);
$ACL->allow(null,  'legislationTypes', 'index');


// Allow Staff to do stuff
$ACL->allow('Appointer', 'committees',     'applications', $requiresCommitteeAssociation); // committee_id
$ACL->allow('Appointer', 'applicantFiles', 'download',     $requiresCommitteeAssociation); // applicantFile_id
$ACL->allow('Appointer', 'applicants',     'view',         $requiresCommitteeAssociation); // applicant_id
$ACL->allow('Appointer', 'applications',   'report',       $requiresCommitteeAssociation); // committee_id
$ACL->allow('Appointer', 'people',         'viewContactInfo');

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


$ACL->allow('Liaison', 'members', ['index', 'appoint', 'reappoint', 'resign', 'update'], $requiresCommitteeAssociation);

// Administrator is allowed access to everything
$ACL->allow('Administrator');
