<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 * @param array  $this->offices
 */
use Application\Models\Person;

$member_id = $this->member->getId();
$links     = [];

$userCanEditOffices = (!$this->disableButtons && Person::isAllowed('offices', 'update'));
if ($userCanEditOffices) {
    $uri = BASE_URI."/offices/update?committee_id={$this->member->getCommittee_id()};person_id={$this->member->getPerson_id()}";
    $links[] = ['url'=>$uri, 'label'=>$this->_('office_add')];
}
foreach ($this->offices as $office) {
    $title = self::escape($office->getTitle());

    if ($userCanEditOffices) {
        $uri     = BASE_URI."/offices/update?office_id={$office->getId()}";
        $links[] = ['url'=>$uri, 'label'=>sprintf($this->_('office_edit', 'messages'), $title)];
    }
}

$editButton   = null;
$deleteButton = null;
if (Person::isAllowed('members', 'update')) {
    $uri     = BASE_URI."/members/update?member_id=$member_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('member_edit')];
}
if (Person::isAllowed('members', 'delete')) {
    $uri     = BASE_URI."/members/delete?member_id=$member_id";
    $links[] = ['url'=>$uri, 'label'=>$this->_('member_delete')];
}

if (count($links)) {
    $helper = $this->template->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('more'), "memberActions_$member_id");
}
