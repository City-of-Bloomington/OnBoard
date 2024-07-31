<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Report;

use Application\Models\CommitteeTable;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $table = new CommitteeTable();
        $list  = $table->find(['current'=>true]);

        $committees = [];
        $members    = [];
        foreach ($list as $i=>$c) {
            $committees[] = $c;

            if ($c->getType() == 'seated') {
                $data = SeatTable::currentData(['committee_id'=>$c->getId()]);
                foreach ($data['results'] as $row) { $members[$i][] = $row; }
            }
            else {
                $results = $c->getMembers(['current' => true]);
                foreach ($results as $member) { $members[$i][] = $member; }
            }

        }

        return new View($committees, $members);
    }
}
