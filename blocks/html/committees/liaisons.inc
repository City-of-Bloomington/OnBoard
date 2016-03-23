<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Committee;
use Application\Models\Person;

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
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['liaison', 'liaisons', 2]); ?></h1>
        <div class="fn1-uiBlock-headerTools"><?= $addButton; ?></div>
    </header>
    <?php
        $this->data = Committee::liaisonData(['committee_id'=>$this->committee->getId()]);
        $this->_include('liaisons/partials/dataTable.inc');
    ?>
</section>
