<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->members
 * @param string $this->title (optional)
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
$title = $this->title
    ? self::escape($this->title)
    : $this->_(['member', 'members', count($this->members)]);
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header"><h1><?= $title; ?></h1></header>
    <div    class="fn1-uiBlock-content">

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
        foreach ($this->members as $m) {
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
    if ($this->committee) {

        if (Person::isAllowed('members', 'update')) {
            $uri = BASE_URI.'/members/update?committee_id='.$this->committee->getId();
        }
        elseif (Person::isAllowed('members', 'appoint')) {
            $uri = BASE_URI.'/members/appoint?committee_id='.$this->committee->getid();
        }
        
        if (isset($uri)) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink($uri, $this->_('member_add'), 'add');
        }
    }
?>
    </div>
</section>