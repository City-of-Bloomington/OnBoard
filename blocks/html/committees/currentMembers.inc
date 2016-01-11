<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\Url;
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<div>
	<h2><?= $this->_(['current_member', 'current_members', 2]); ?></h2>
	<table>
		<thead>
			<tr><th><?= $this->_('name'); ?></th>
				<th><?= $this->_('appointed_by'); ?></th>
				<th><?= $this->_(['term','term', 1]); ?></th>
				<th><?= $this->_(['office','offices', 2]); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$currentUrl = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			$helper = $this->template->getHelper('buttonLink');

            $committee_id = $this->committee->getId();


            if ($this->committee->getType() === 'seated') {
                foreach ($this->committee->getSeats(time()) as $seat) {
                    $appointer = $seat->getAppointer_id()
                        ? self::escape($seat->getAppointer()->getName())
                        : '';
                    $member = $seat->getMember(time());
                    if ($member) {
                        $name = self::escape($member->getPerson()->getFullname());
                        $term = $member->getTerm_id()
                            ? "{$member->getTerm()->getStartDate(DATE_FORMAT)} - {$member->getTerm()->getEndDate(DATE_FORMAT)}"
                            : '';
                        $offices = [];
                        foreach ($member->getPerson()->getOffices($this->committee, date('Y-m-d')) as $office) {
                            $offices[] = self::escape($office->getTitle());
                        }
                        $offices = implode(',',$offices);
                        echo "
                        <tr><td>$name</td>
                            <td>$appointer</td>
                            <td>$term</td>
                            <td>$offices</td>
                            <td></td>
                        </tr>
                        ";
                    }
                    else {
                        $t = $seat->getTerm(time());
                        $term = $t ? "{$t->getStartDate(DATE_FORMAT)} - {$t->getEndDate(DATE_FORMAT)}" : '';

                        echo "
                        <tr><td>{$this->_('vacancy')}</td>
                            <td>$appointer</td>
                            <td>$term</td>
                            <td></td>
                            <td></td>
                        </tr>
                        ";
                    }
                }
            }
            else {
                // Display current committee members
                // These members will not be associated with a seat
                foreach ($this->committee->getMembers() as $m) {
                    $name = self::escape($m->getPerson()->getFullname());
                    echo "
                    <tr><td>$name</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    ";
                }
            }
		?>
		</tbody>
	</table>
</div>
