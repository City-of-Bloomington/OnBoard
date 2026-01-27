<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class ApplicantFilesTable extends PdoRepository
{
    const TABLE = 'applicantFiles';

    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\ApplicantFile'); }

    public function find(array $fields=[], ?string $order='updated desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from applicantFiles';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    default:
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
     * Check if the user shares a committee with the file's applicant.
     */
    public function shareCommittee(int $user_id, int $file_id): bool
    {
        $sql    = "select a.committee_id
                   from applicantFiles f
                   join applications   a on f.person_id=a.person_id
                   join members        m on a.committee_id=m.committee_id and (m.endDate is null or m.endDate > now())
                   where m.person_id=? and  f.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$user_id, $file_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }

    /**
     * Check if an applicant is for a given department
     */
    public function hasDepartment(int $department_id, int $file_id): bool
    {
        $sql    = "select c.department_id
                    from applicantFiles f
                    join applications   a on f.person_id=a.person_id
                    join committee_departments c on a.committee_id=c.committee_id
                    where c.department_id=? and f.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $file_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
