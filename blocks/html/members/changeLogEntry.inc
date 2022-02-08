<?php
/**
 * @copyright 2017-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
        $seat   = null;
        $term   = null;
        $person = null;

        if (!empty($change['seat_id'])) {
            try { $seat = new Seat($change['seat_id']); }
            catch (\Exception $e) {
                // The seat may no longer exist
            }
        }

        if (!empty($change['term_id'])) {
            try { $term = new Term($change['term_id']); }
            catch (\Exception $e) {
                // The term may no longer exist
            }
        }

        if (!empty($change['person_id'])) {
            try                  { $person = new Person($change['person_id']); }
            catch(\Exception $e) { $person = new Person(['firstname'=>'Unknown', 'lastname'=>'Person']); }
        }

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
