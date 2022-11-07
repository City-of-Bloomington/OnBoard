<?php
/**
 * @copyright 2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Alternate $this->alternate
 */
use Application\Models\Person;

$alternate_id = $this->alternate->getId();
$links        = [];

$editButton   = null;
$deleteButton = null;
if (Person::isAllowed('alternates', 'update')) {
    $uri     = parent::generateUri('alternates.update')."?alternate_id=$alternate_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('alternate_edit')];
}
if (Person::isAllowed('alternates', 'delete')) {
    $uri     = parent::generateUri('alternates.delete')."?alternate_id=$alternate_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('alternate_delete')];
}

if (count($links)) {
    $helper = $this->template->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('more'), "alternateActions_$alternate_id");
}
