<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;

include '../configuration.inc';
$person = new Person();
$person->setFirstname('Administrator');
$person->setLastname('Person');
$person->setEmail('admin@example.org');

$person->setUsername('admin');
//$person->setPassword();
$person->setAuthenticationMethod('Employee');
$person->setRole('Administrator');

$person->save();
