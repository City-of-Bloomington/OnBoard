<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
?>
<table class="fn1-table">
    <thead>
        <tr><th></th>
            <th></th>
            <th><?= $this->_('actions'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        // Display current committee members
        // These members will not be associated with a seat
        foreach ($this->committee->getMembers() as $m) {
            $name = self::escape($m->getPerson()->getFullname());

            echo "
            <tr><td>$name</td>
                <td>{$m->getStartDate(DATE_FORMAT)} - {$m->getEndDate(DATE_FORMAT)}</td>
                <td></td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
<?php
    if (Person::isAllowed('members', 'appoint')) {
        $helper = $this->template->getHelper('buttonLink');
        echo $helper->buttonLink(
            BASE_URI.'/members/appoint?committee_id='.$this->committee->getid(),
            $this->_('member_add'),
            'add'
        );
    }
?>