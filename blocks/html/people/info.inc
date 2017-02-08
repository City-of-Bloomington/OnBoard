<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person  $this->person
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Michelf\MarkdownExtra;

$person_id = $this->person->getId();
$helper    = $this->template->getHelper('buttonLink');

$editButton   = '';
$deleteButton = '';
if (!$this->disableButtons) {
    if (Person::isAllowed('people', 'edit')) {
        $editButton = $helper->buttonLink(
            BASE_URI.'/people/update?person_id='.$person_id,
            $this->_('person_edit'),
            'edit'
        );
    }
    if ($this->person->isSafeToDelete() && Person::isAllowed('people', 'delete')) {
        $deleteButton = $helper->buttonLink(
            BASE_URI.'/people/delete?person_id='.$person_id,
            $this->_('person_delete'),
            'delete'
        );
    }
}

$fullname = self::escape($this->person->getFullname());
$markdown = new MarkdownExtra();
$about = $markdown->transform($this->person->getAbout());
echo "
<section>
    <header>
        <h1>$fullname</h1>
        <div class=\"tools\">$editButton $deleteButton</div>
    </header>

    <div>
        <p>$about</p>
";
        $fields = [
            'username'=> $this->_('username'),
            'gender'  => $this->_('gender'),
            'race'    => $this->_('race'),
        ];
        if (Person::isAllowed('people', 'viewContactInfo')) {
            $fields['email'  ] = $this->_('email'  );
            $fields['phone'  ] = $this->_('phone'  );
            $fields['address'] = $this->_('address');
            $fields['city'   ] = $this->_('city'   );
            $fields['state'  ] = $this->_('state'  );
            $fields['zip'    ] = $this->_('zip'    );
        }
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
                <dl>
                    <dt>$fields[$key]</dt>
                    <dd>$value</dd>
                </dl>";
            }
        }
echo "
    </div>
</section>
";
