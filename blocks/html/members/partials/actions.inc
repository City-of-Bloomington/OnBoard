<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 * @param array  $this->offices
 */
use Application\Models\Person;

$member_id = $this->member->getId();

$officeGroup = ['subgroup'=>[]];
if (Person::isAllowed('offices', 'edit')) {
    $uri = BASE_URI."/offices/update?committee_id={$this->member->getCommittee_id()};person_id={$this->member->getPerson_id()}";
    $officeGroup['subgroup'][] = ['url'=>$uri, 'label'=>$this->_('office_add')];
}
foreach ($this->offices as $office) {
    $title = self::escape($office->getTitle());

    if (!$this->disableButtons && Person::isAllowed('offices','edit')) {
        $uri = BASE_URI."/offices/update?office_id={$office->getId()}";
        $officeGroup['subgroup'][] = ['url'=>$uri, 'label'=>sprintf($this->_('office_edit', 'messages'), $title)];
    }
}

$editButton   = ['subgroup'=>[]];
$deleteButton = ['subgroup'=>[]];
if (Person::isAllowed('members', 'edit')) {
    $uri = BASE_URI."/members/update?member_id=$member_id";
    $editButton['subgroup'][] = ['url'=>$uri, 'label'=>$this->_('member_edit')];
}
if (Person::isAllowed('members', 'delete')) {
    $uri = BASE_URI."/members/delete?member_id=$member_id";
    $deleteButton['subgroup'][] = ['url'=>$uri, 'label'=>$this->_('member_delete')];
}

$links = [];
if (count($officeGroup ['subgroup'])) { $links[] = $officeGroup; }
if (count($editButton  ['subgroup'])) { $links[] = $editButton; }
if (count($deleteButton['subgroup'])) { $links[] = $deleteButton; }
if (count($links)) {
    $helper = $this->template->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('more'), "memberActions_$member_id");
}
