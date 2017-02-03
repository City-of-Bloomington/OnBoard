<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

include '../bootstrap.inc';
$person = new Person();
$person->setFirstname('{{ onboard_admin.firstname }}');
$person->setLastname ('{{ onboard_admin.lastname  }}');
$person->setEmail    ('{{ onboard_admin.email     }}');
$person->setUsername ('{{ onboard_admin.username  }}');
//$person->setPassword();
$person->setAuthenticationMethod('Employee');
$person->setRole('Administrator');

$person->save();
