<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\Person;
use Blossom\Classes\View;

$username = $this->person->getUsername();
if (!$username && Person::isAllowed('users')) {
	$username = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-plus"></a> %s</a>',
		BASE_URI.'/users/update?user_id='.$this->person->getId(),
		$this->translate('labels.create_account')
	);
}

$editButton = '';
if (Person::isAllowed('people', 'edit')) {
	$editButton = sprintf(
		'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
		BASE_URI.'/people/update?person_id='.$this->person->getId(),
		$this->translate('labels.edit_person')
	);
}

$fullname = View::escape($this->person->getFullname());
$about    = View::escape($this->person->getAbout());
$birthdate = $this->person->getBirthdate(DATE_FORMAT);
echo "
<div>
	<h2>$fullname $editButton</h2>
	<p>$about</p>
	<table>
";
	$fields = [
		'email'   => $this->_(['labels.email', 'labels.emails', 1]),
		'username'=> $this->_('labels.username'),
		'address' => $this->_('labels.address'),
		'city'    => $this->_('labels.city'),
		'zipcode' => $this->_('labels.zipcode'),
		'gender'  => $this->_('labels.gender'),
		'race'    => $this->_(['labels.race', 'labels.races', 1]),
		'birthdate'=>$this->_('labels.birthdate')
	];
	foreach ($fields as $key=>$label) {
		if ($key == 'birthdate') {
			$value = View::escape($this->person->getBirthdate(DATE_FORMAT));
		}
		else {
			$get = 'get'.ucfirst($key);
			$value = View::escape($this->person->$get());
		}
		if ($value) {
			echo "<tr><th>$fields[$key]</th><td>$value</td></tr>";
		}
	}
echo "
	</table>
</div>
";
