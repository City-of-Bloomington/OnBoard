<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Seat $this->seat
 */
$termLength = new \DateInterval($this->seat->getTermLength());

$currentTerm = $this->seat->getTerm(time());
if ($currentTerm) {
    $defaultTermId = $currentTerm->getId();

    $d = new \DateTime();
    $d->add($termLength);
    $nextTerm = $this->seat->getTerm((int)$d->format('U'));

    $list = [$nextTerm, $currentTerm];

    $d = new \DateTime();
    $d->sub($termLength);
    $previousTerm = $this->seat->getTerm((int)$d->format('U'));
    if ($previousTerm) {
        $list[] = $previousTerm;
    }

    $options = [];
    foreach ($list as $term) {
        if ($term->isVacant()) {
            $dates     = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
            $options[] = ['value'=>$term->getId(), 'label'=>$dates];
        }
    }
    $helper = $this->template->getHelper('field');
    echo $helper->field([
        'name'     => 'newMember[term_id]',
        'id'       => 'newMember-term_id',
        'label'    => $this->_('term'),
        'value'    => $currentTerm->getId(),
        'type'     => 'select',
        'options'  => $options,
        'required' => true
    ]);
}
