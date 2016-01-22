<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;

$liasons = $this->committee->getLiasons();

$addButton = '';
if (Person::isAllowed('liasons', 'add')) {
    $helper    = $this->template->getHelper('buttonLink');
    $addButton = $helper->buttonLink(
        BASE_URI.'/liasons/add?committee_id='.$this->committee->getId(),
        $this->_('liason_add'),
        'add'
    );
}
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['liason', 'liasons', count($liasons)]); ?></h1>
        <div class="fn1-uiBlock-headerTools"><?= $addButton; ?></div>
    </header>
    <table class="fn1-table">
    <?php
        $userCanRemove = Person::isAllowed('liasons', 'remove');
        $removeButton = '';

        foreach ($liasons as $person) {
            $name  = self::escape($person->getFullname());
            $email = self::escape($person->getEmail());
            $phone = self::escape($person->getPhone());

            $uri = BASE_URI.'/people/view?person_id='.$person->getId();

            if ($userCanRemove) {
                $removeButton = $helper->buttonLink(
                    BASE_URI."/liasons/remove?committee_id={$this->committee->getId()};person_id={$person->getId()}",
                    $this->_('liason_remove'),
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
