<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->change  Array of JSON data for the change
 */
declare (strict_types=1);
use Application\Models\CommitteeHistory;
use Application\Models\Seat;
use Application\Models\Term;
use Application\Models\Person;

foreach (CommitteeHistory::$states as $state) {
    if (    isset($this->change[$state]) && count($this->change[$state])) {
        $change = $this->change[$state];
        $seat   = !empty($change['seat_id'  ]) ? new Seat  ($change['seat_id'  ]) : null;
        $term   = !empty($change['term_id'  ]) ? new Term  ($change['term_id'  ]) : null;
        $person = !empty($change['person_id']) ? new Person($change['person_id']) : null;

        $$state = [
            'id'        => !empty($change['id']) ? $change['id'] : '',
            'person'    => $person ? parent::escape($person->getFullname()) : '',
            'seat'      => $seat   ? parent::escape($seat->getName()) : '',
            'term'      => $term   ? "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}" : '',
            'startDate' => isset($change['startDate']) ? $change['startDate'] : '',
            'endDate'   => isset($change['endDate'  ]) ? $change['endDate'  ] : ''
        ];
        if (!isset($keys)) { $keys = array_keys($$state); }
    }
    else { unset($$state); }
}
?>
<table>
    <thead>
        <tr><th></th>
        <?php
            foreach (CommitteeHistory::$states as $state) {
                if (isset($$state)) { echo "<th>{$this->_($state)}</th>"; }
            }
        ?>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($keys as $k) {
            echo "<tr><th>$k</th>";
            foreach (CommitteeHistory::$states as $state) {
                if (isset($$state)) { echo "<td>{$$state[$k]}</td>"; }
            }
            echo '</tr>';
        }
    ?>
    </tbody>
</table>
