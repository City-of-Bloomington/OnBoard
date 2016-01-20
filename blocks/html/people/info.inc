<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
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

$fullname = self::escape($this->person->getFullname());
$markdown = new MarkdownExtra();
$about = $markdown->transform($this->person->getAbout());
echo "
<section     class=\"fn1-uiBlock\">
    <header  class=\"fn1-uiBlock-header\"><h1>$fullname</h1>
        <div class=\"fn1-uiBlock-headerTools\">$editButton</div>
    </header>

    <div class=\"fn1-uiBlock-content\">
        <p>$about</p>
";
        $fields = [
            'email'   => $this->_(['email', 'emails', 1]),
            'username'=> $this->_('username'),
            'gender'  => $this->_('gender'),
            'race'    => $this->_(['race', 'races', 1]),
        ];
        foreach ($fields as $key=>$label) {
            $get = 'get'.ucfirst($key);
            $value = self::escape($this->person->$get());

            if ($value) {
                echo "
                <dl class=\"fn1-output-field\">
                    <dt>$fields[$key]</dt>
                    <dd>$value</dd>
                </dl>";
            }
        }
echo "
    </div>
</section>
";
