<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\History;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;

class View extends \Web\View
{
    private static $keys = [
        'id', 'type', 'name', 'statutoryName', 'code',
        'yearFormed', 'endDate', 'calendarId', 'website', 'videoArchive',
        'email', 'phone', 'address', 'city', 'state', 'zip',
        'description', 'meetingSchedule', 'termEndWarningDays', 'applicationLifetime', 'legislative'
    ];

    public function __construct(Committee $committee)
    {
        parent::__construct();

        $history = [];
        foreach ($committee->getHistory() as $h) {
            $changes = [];
            foreach ($h->getChanges() as $c) {
                $change = [];
                foreach (self::$keys as $k) {
                    foreach (CommitteeHistory::$states as $s) {
                        if (!empty($c[$s][$k])) {
                            $change[$k][$s] = $c[$s][$k];
                        }
                    }
                }
                $changes[] = $change;
            }

            $history[] = [
                'date'      => $h->getDate(),
                'person'    => $h->getPerson()->getFullname(),
                'tablename' => $h->getTablename(),
                'action'    => $h->getAction(),
                'changes'   => $changes
            ];
        }

        $this->vars = [
            'committee' => $committee,
            'history'   => $history,
            'states'    => CommitteeHistory::$states
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/history.twig", $this->vars);
    }
}
