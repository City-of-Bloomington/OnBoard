<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Dan Hiester <hiesterd@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->terms
 * @param Seat $this->seat (optional)
 */
 use Application\Models\Person;
 use Application\Templates\Helpers\ButtonLink;
 use Blossom\Classes\Block;
?>

<h2><?= $this->_('recent_appointees') ?></h2>
<?php foreach($this->terms as $term): ?>
    <?php if(count($term->getMembers()) > 0): ?>
        <div>
        Firstname Surname <span class="onboard-dates"><?= $term->getStartDate(DATE_FORMAT) ?>&ndash;<?= $term->getEndDate(DATE_FORMAT) ?>   </span>
        </div>
    <?php else: ?>
        <div>No appointees found for term beginning on <?= $term->getStartDate(DATE_FORMAT) ?> and ending <?= $term->getEndDate(DATE_FORMAT) ?>.</div>
    <?php endif ?>
<?php endforeach ?>
