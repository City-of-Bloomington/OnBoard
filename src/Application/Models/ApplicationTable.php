<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;
use Web\ActiveRecord;

class ApplicationTable extends PdoRepository
{
    public const DEFAULT_SORT = 'a.created desc, p.lastname, p.firstname';
    public static $searchable_fields = ['firstname', 'lastname', 'email', 'committee_id', 'current', 'archived', 'since'];

    public function __construct() { parent::__construct('applications', __namespace__.'\Application'); }

    public function find(array $fields=[], ?string $order=self::DEFAULT_SORT, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select a.* from applications a';
        $joins  = ['join people p on p.id=a.person_id'];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    // Datetime values are expected to be timestamps
                    case 'current':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $v);
                        $where[] = "(a.archived is null or a.archived>='$date')";
                        break;

                    case 'archived':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $v);
                        $where[] = "(a.archived is not null and a.archived<='$date')";
                        break;

                    case 'since':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $v);
                        $where[] = "a.created>'$date'";
                        break;

                    default:
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function search(array $fields=[], ?string $order=self::DEFAULT_SORT, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select a.* from applications a';
        $joins  = ['join people p on p.id=a.person_id'];
        $where  = [];
        $params = [];

        foreach (self::$searchable_fields as $f) {
            if (!empty($fields[$f])) {
                switch ($f) {
                    case 'current':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $fields[$f]);
                        $where[] = "(a.archived is null or a.archived>='$date')";
                        break;

                    case 'archived':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $fields[$f]);
                        $where[] = "(a.archived is not null and a.archived<='$date')";
                        break;

                    case 'committee_id':
                        $where[] = "$f=:$f";
                        $params[$f] = $fields[$f];
                    break;

                    case 'email':
                        $joins[] = 'join people_emails e on p.id=e.person_id';
                        $where[] = "$f like :$f";
                        $params[$f] = $fields['email'].'%';
                    break;

                    default:
                        $where[] = "$f like :$f";
                        $params[$f] = $fields[$f].'%';
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function hasDepartment(int $department_id, int $application_id): bool
    {
        $sql    = "select a.committee_id
                   from applications a
                   join committee_departments d on a.committee_id=d.committee_id
                   where d.department_id=? and a.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $application_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
