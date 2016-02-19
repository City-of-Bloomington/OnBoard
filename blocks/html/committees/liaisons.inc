<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;

$liaisons = $this->committee->getLiaisonPeople();

$addButton = '';
if (Person::isAllowed('liaisons', 'add')) {
    $helper    = $this->template->getHelper('buttonLink');
    $addButton = $helper->buttonLink(
        BASE_URI.'/liaisons/add?committee_id='.$this->committee->getId(),
        $this->_('liaison_add'),
        'add'
    );
}
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['liaison', 'liaisons', count($liaisons)]); ?></h1>
        <div class="fn1-uiBlock-headerTools"><?= $addButton; ?></div>
    </header>
    <table class="fn1-table">
    <?php
        $userCanRemove = Person::isAllowed('liaisons', 'remove');
        $removeButton = '';

        foreach ($liaisons as $person) {
            $name  = self::escape($person->getFullname());
            $email = self::escape($person->getEmail());
            $phone = self::escape($person->getPhone());

            $uri = BASE_URI.'/people/view?person_id='.$person->getId();

            if ($userCanRemove) {
                $removeButton = $helper->buttonLink(
                    BASE_URI."/liaisons/remove?committee_id={$this->committee->getId()};person_id={$person->getId()}",
                    $this->_('liaison_remove'),
                    'delete'
                );
            }

            echo "
            <tr><td><a href=\"$uri\">$name</a></td>
                <td>$email</td>
                <td>$phone</td>
                <td>$removeButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
