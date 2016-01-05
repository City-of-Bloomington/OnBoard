<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$helper = $this->template->getHelper('buttonLink');

$username = $this->person->getUsername();
if (!$username && Person::isAllowed('users')) {
	$username = $helper->buttonLink(
		BASE_URI.'/users/update?user_id='.$this->person->getId(),
		$this->translate('create_account'),
		'add'
	);
}

$editButton = '';
if (Person::isAllowed('people', 'edit')) {
	$editButton = $helper->buttonLink(
		BASE_URI.'/people/update?person_id='.$this->person->getId(),
		$this->translate('edit_person'),
		'edit',
		ButtonLink::SIZE_ICON
	);
}

$fullname = View::escape($this->person->getFullname());
$markdown = new MarkdownExtra();
$about = $markdown->transform($this->person->getAbout());
echo "
<div>
	<h2>$fullname $editButton</h2>
	<p>$about</p>
	<table>
";
	$fields = [
		'email'   => $this->_(['email', 'emails', 1]),
		'username'=> $this->_('username'),
		'gender'  => $this->_('gender'),
		'race'    => $this->_(['race', 'races', 1]),
	];
	foreach ($fields as $key=>$label) {
		$get = 'get'.ucfirst($key);
		$value = View::escape($this->person->$get());

		if ($value) {
			echo "<tr><th>$fields[$key]</th><td>$value</td></tr>";
		}
	}
echo "
	</table>
</div>
";
