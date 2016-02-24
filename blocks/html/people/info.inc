<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Michelf\MarkdownExtra;

$helper = $this->template->getHelper('buttonLink');

$editButton = '';
if (!$this->disableButtons && Person::isAllowed('people', 'edit')) {
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
<section>
    <header>
        <h1>$fullname</h1>
        <div class=\"tools\">$editButton</div>
    </header>

    <div>
        <p>$about</p>
";
        $fields = [
            'email'   => $this->_('email'),
            'phone'   => $this->_('phone'),
            'username'=> $this->_('username'),
            'gender'  => $this->_('gender'),
            'race'    => $this->_('race'),
        ];
        foreach ($fields as $key=>$label) {
            $get = 'get'.ucfirst($key);
            $value = self::escape($this->person->$get());

            if ($key === 'username' && !$value && !$this->disableButtons && Person::isAllowed('users')) {
                $value = $helper->buttonLink(
                    BASE_URI.'/users/update?user_id='.$this->person->getId(),
                    $this->_('create_account'),
                    'add'
                );
            }

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
