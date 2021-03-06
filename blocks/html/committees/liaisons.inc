<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Committee $this->committee
 */
use Application\Models\LiaisonTable;
use Application\Models\Person;

$addButton = '';
if (Person::isAllowed('liaisons', 'update')) {
    $helper    = $this->template->getHelper('buttonLink');
    $addButton = $helper->buttonLink(
        parent::generateUri('liaisons.update').'?committee_id='.$this->committee->getId(),
        $this->_('liaison_add'),
        'add'
    );
}
?>
<section>
    <header>
        <h2><?= $this->_(['liaison', 'liaisons', 2]); ?></h2>
        <div class="tools"><?= $addButton; ?></div>
    </header>
    <?php
        $this->data = LiaisonTable::committeeLiaisonData(['committee_id'=>$this->committee->getId()]);
        $this->_include('liaisons/partials/dataTable.inc');
    ?>
</section>
