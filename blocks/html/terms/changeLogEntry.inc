<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
use Application\Models\Seat;
use Application\Models\CommitteeHistory;

foreach (CommitteeHistory::$states as $state) {
    if (    isset($this->change[$state]) && count($this->change[$state])) {
        $change = $this->change[$state];
        $seat   = !empty($change['seat_id'  ]) ? new Seat  ($change['seat_id'  ]) : null;

        $$state = [
            'id'        => !empty($change['id'       ]) ? $change['id'        ] : '',
            'seat'      => $seat ? parent::escape($seat->getName())             : '',
            'startDate' => !empty($change['startDate']) ? $change['startDate' ] : '',
            'endDate'   => !empty($change['endDate'  ]) ? $change['endDate'   ] : ''
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
