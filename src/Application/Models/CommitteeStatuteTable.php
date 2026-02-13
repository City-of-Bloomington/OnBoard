<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class CommitteeStatuteTable extends PdoRepository
{
    public function __construct() { parent::__construct('committeeStatutes', __namespace__.'\CommitteeStatute'); }

    public function hasDepartment(int $department_id, int $statute_id): bool
    {
        $sql    = "select s.committee_id
                   from committeeStatutes s
                   join committee_departments d on s.committee_id=d.committee_id
                   where d.department_id=? and s.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $statute_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
