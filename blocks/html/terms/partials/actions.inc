<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Term $this->term
 */
use Application\Models\Person;

$term_id = $this->term->getId();

$editButton   = ['subgroup'=>[]];
$deleteButton = ['subgroup'=>[]];

if (Person::isAllowed('terms', 'edit')) {
    $uri = BASE_URI."/terms/update?term_id=$term_id";
    $editButton['subgroup'][] = ['url'=>$uri, 'label'=>$this->_('term_edit')];
}
if (Person::isAllowed('terms', 'delete') && $this->term->isSafeToDelete()) {
    $uri = BASE_URI."/terms/delete?term_id=$term_id";
    $deleteButton['subgroup'][] = ['url'=>$uri, 'label'=>$this->_('term_delete')];
}

$links = [];
if (count($editButton  ['subgroup'])) { $links[] = $editButton; }
if (count($deleteButton['subgroup'])) { $links[] = $deleteButton; }
if (count($links)) {
    $helper = $this->template->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('more'), "termActions_$term_id");
}
