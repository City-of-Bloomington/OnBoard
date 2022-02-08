<?php
/**
 * @copyright 2017-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Models\Appointer;
use Application\Models\CommitteeHistory;

foreach (CommitteeHistory::$states as $state) {
    if (    isset($this->change[$state]) && count($this->change[$state])) {
        $change    = $this->change[$state];
        $appointer = null;

        if (!empty($change['appointer_id'])) {
            try { $appointer = new Appointer($change['appointer_id']); }
            catch (\Exception $e) {
                // The appointer may no longer exist
            }
        }

        $$state = [
            'id'           => !empty($change['id'  ]) ? $change['id'] : '',
            'type'         => !empty($change['type']) ? parent::escape($change['type']) : '',
            'code'         => !empty($change['code']) ? parent::escape($change['code']) : '',
            'name'         => !empty($change['name']) ? parent::escape($change['name']) : '',
            'appointer'    => $appointer              ? parent::escape($appointer->getName()) : '',
            'startDate'    => !empty($change['startDate'   ]) ? $change['startDate' ] : '',
            'endDate'      => !empty($change['endDate'     ]) ? $change['endDate'   ] : '',
            'termLength'   => !empty($change['termLength'  ]) ? $change['termLength'] : '',
            'voting'       => !empty($change['voting'      ]) ? $change['voting'    ] : '',
            'requirements' => !empty($change['requirements']) ? parent::escape($change['requirements']) : ''
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
