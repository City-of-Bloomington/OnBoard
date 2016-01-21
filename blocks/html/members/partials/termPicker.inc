<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
$termLength = new \DateInterval($this->seat->getTermLength());

$currentTerm = $this->seat->getTerm(time());
if ($currentTerm) {
    $d = new \DateTime();
    $d->add($termLength);
    $nextTerm = $this->seat->getTerm($d->format('U'));

    $list = [$nextTerm, $currentTerm];

    $d = new \DateTime();
    $d->sub($termLength);
    $previousTerm = $this->seat->getTerm($d->format('U'));
    if ($previousTerm) {
        $list[] = $previousTerm;
    }

    $options = '';
    foreach ($list as $term) {
        if ($term->isVacant()) {
            $dates   = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
            $options.= "<option value=\"{$term->getId()}\">$dates</option>";
        }
    }

    echo "
    <dl class=\"fn1-input-field--select\"><dt><label   for=\"newMember-term_id\" class=\"required\">{$this->_('term')}</label></dt>
        <dd><select name=\"newMember[term_id]\" id=\"newMember-term_id\">
                $options
            </select>
        </dd>
    </dl>
    ";
}
