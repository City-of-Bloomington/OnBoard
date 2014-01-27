<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\Person;

$person = [
	'id'=>$this->person->getId(),
	'fullname'=>$this->person->getFullname()
];

$fields = [
	'about',
	'email',
	'username',
	'address',
	'city',
	'zipcode',
	'gender',
	'race',
	'birthdate'
];
foreach ($fields as $key) {
	if ($key == 'birthdate') {
		$value = $this->person->getBirthdate(DATE_FORMAT);
	}
	else {
		$get = 'get'.ucfirst($key);
		$value = $this->person->$get();
	}

	if ($value) { $person[$key] = $value; }
}
echo json_encode($person);
