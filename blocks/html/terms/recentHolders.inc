<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Dan Hiester <hiesterd@bloomington.in.gov>
 * @param array $this->terms
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;
?>
<section>
<?php
    foreach ($this->terms as $term) {
        $members = [];
        foreach ($term->getMembers() as $m) {
            $members[] = $m->getPerson()->getFullname();
        }
        $members = implode(', ', $members);

        echo "
        <div>
            <span class=\"onboard-dates\">
                {$term->getStartDate(DATE_FORMAT)}-{$term->getEndDate(DATE_FORMAT)}
            </span>
            $members
        </div>
        ";
    }
?>
</section>
