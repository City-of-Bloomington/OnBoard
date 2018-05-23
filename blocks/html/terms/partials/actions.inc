<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Term $this->term
 */
use Application\Models\Person;

$term_id = $this->term->getId();

$links = [];

if (Person::isAllowed('terms', 'update')) {
    $uri     = BASE_URI."/terms/update?term_id=$term_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('term_edit')];
}
if (Person::isAllowed('terms', 'delete') && $this->term->isSafeToDelete()) {
    $uri     = BASE_URI."/terms/delete?term_id=$term_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('term_delete')];
}

if (count($links)) {
    $helper = $this->template->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('more'), "termActions_$term_id");
}
