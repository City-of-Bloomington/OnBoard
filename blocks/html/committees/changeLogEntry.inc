<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
use Application\Models\Appointer;
use Application\Models\CommitteeHistory;

$keys = [
    'id', 'type', 'name', 'statutoryName', 'code',
    'yearFormed', 'endDate', 'calendarId', 'website', 'videoArchive',
    'email', 'phone', 'address', 'city', 'state', 'zip',
    'description', 'meetingSchedule', 'termEndWarningDays', 'applicationLifetime', 'legislative'
];
foreach (CommitteeHistory::$states as $state) {
    if (    isset($this->change[$state]) && count($this->change[$state])) {
        $change = $this->change[$state];
        $$state = [];
        foreach ($keys as $k) {
            $$state[$k] = !empty($change[$k]) ? parent::escape($change[$k]) : '';
        }
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
