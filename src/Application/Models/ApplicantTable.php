<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class ApplicantTable extends PdoRepository
{
    public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

    public static $searchable_fields = ['firstname', 'lastname', 'email', 'committee_id'];

    public function search(array $fields=[], string $order='p.lastname, p.firstname', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select p.* from people p';
        $joins  = ['join applications a on p.id=a.person_id'];
        $where  = [];
        $group  = 'p.id';
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                if ($v && in_array($k, self::$searchable_fields)) {
                    switch ($k) {
                        case 'email':
                            $joins[] = 'join people_emails e on p.id=e.person_id';
                            $where[] = "e.$k like :$k";
                            $params[$k] = "$v%";
                        break;

                        case 'committee_id':
                            $where[] = "a.$k=:$k";
                            $params[$k] = $v;
                        break;

                        default:
                            $where[] = "p.$k like :$k";
                            $params[$k] = "$v%";
                    }
                }
            }
        }

        $sql  = parent::buildSql($select, $joins, $where, $group, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
     * Check if the user shares a committee with the applicant
     */
    public function shareCommittee(int $user_id, int $applicant_id): bool
    {
        $sql    = "select a.committee_id
                   from applications a
                   join members      m on a.committee_id=m.committee_id and (m.endDate is null or m.endDate > now())
                   where m.person_id=? and a.applicant_id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$user_id, $applicant_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }

    /**
     * Check if an applicant is for a given department
     */
    public function hasDepartment(int $department_id, int $applicant_id): bool
    {
        $sql    = "select c.department_id
                    from applications          a
                    join committee_departments c on a.committee_id=c.committee_id
                    where c.department_id=? and a.applicant_id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $applicant_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
