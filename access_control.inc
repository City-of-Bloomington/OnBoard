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
		 ->addRole(new Role('Public'), 'Anonymous')
		 ->addRole(new Role('Staff'), 'Public')
		 ->addRole(new Role('Administrator'), 'Staff');

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

$ZEND_ACL->addResource(new Resource('appointers'));
$ZEND_ACL->addResource(new Resource('committees'));
$ZEND_ACL->addResource(new Resource('offices'));
$ZEND_ACL->addResource(new Resource('races'));
$ZEND_ACL->addResource(new Resource('seats'));
$ZEND_ACL->addResource(new Resource('tags'));
$ZEND_ACL->addResource(new Resource('terms'));
$ZEND_ACL->addResource(new Resource('members'));
$ZEND_ACL->addResource(new Resource('liasons'));

$ZEND_ACL->addResource(new Resource('topics'));
$ZEND_ACL->addResource(new Resource('topicTypes'));
$ZEND_ACL->addResource(new Resource('votes'));
$ZEND_ACL->addResource(new Resource('voteTypes'));

/**
 * Assign permissions to the resources
 */

// Permissions for unauthenticated browsing
$ZEND_ACL->allow(null, ['callback', 'login']);
$ZEND_ACL->allow(null, 'index', 'index');
$ZEND_ACL->allow(null, ['people', 'topics', 'liasons'], ['index','view']);
$ZEND_ACL->allow(null, 'committees', ['index','info', 'members']);
$ZEND_ACL->allow(null, 'seats', ['index','view', 'vacancies']);


// Allow Staff to do stuff
$ZEND_ACL->allow('Staff', ['people', 'liasons']);
$ZEND_ACL->allow('Staff', 'members', ['index', 'reappoint', 'resign']);


// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
