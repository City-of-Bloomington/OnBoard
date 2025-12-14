<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class LiaisonTable extends TableGateway
{
    public function __construct() { parent::__construct('liaisons', __namespace__.'\Liaison'); }

    /**
     * These are the fields that will be returned for all *Data functions
     */
    public static $dataFields = [
        'liaison_id'     => 'l.id',
        'committee_id'   => 'c.id',
        'committee'      => 'c.name',
        'committee_code' => 'c.code',
        'type'           => 'l.type',
        'person_id'      => 'p.id',
        'username'       => 'p.username',
        'firstname'      => 'p.firstname',
        'lastname'       => 'p.lastname',
        'email'          => 'e.email',
        'phone'          => 'h.number'
    ];

    /**
     * Return SQL for columns used in the SELECT
     *
     * @return string
     */
    private static function getDataColumns()
    {
        $columns = [];
        foreach (self::$dataFields as $k=>$v) {
            $columns[] = "$v as $k";
        }
        return implode(', ', $columns);
    }

    /**
     * Prepares sql for the WHERE and binds values for all values
     *
     * @param array $fields
     * @return array [$where, $params]
     */
    private static function bindFields(?array $fields=null): array
    {
        $where  = [];
        $params = [];
        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (array_key_exists($k, self::$dataFields)) {
                    $f        = self::$dataFields[$k];
                    $where[]  = "$f=?";
                    $params[] = $v;
                }
                elseif ($k === 'current' && $v) {
                    $where[] = '(c.endDate is null or now() < c.endDate)';
                }
            }
            $where = 'where '.implode(' and ', $where);
        }
        else {
            $where  = '';
            $params = null;
        }
        return [$where, $params];
    }

    /**
     * @param string $sql
     * @param array $params
     */
    private static function performDataSelect($sql, $params)
    {
        $db = Database::getConnection();
        $result = $db->query($sql)->execute($params);
        return [
            'fields'  => array_keys(self::$dataFields),
            'results' => $result
        ];
    }

    /**
     * Returns liaison data for all committees
     *
     * This query does a left joins of liaisons for committees.
     * So, all committees will be represented, but there may be
     * empty fields for the liaison and person information.
     *
     * @param array $fields
     * @return array
     */
    public static function data(?array $fields=null)
    {
        $columns = self::getDataColumns();

        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees         c
                left join liaisons      l on c.id=l.committee_id
                left join people        p on l.person_id=p.id
                left join people_emails e on e.person_id=p.id and e.main=1
                left join people_phones h on h.person_id=p.id and h.main=1
                $where
                order by c.name";
        return self::performDataSelect($sql, $params);
    }

    /**
     * This query uses a straight join on committees
     *
     * If a committee does not have any liaisons, that committee
     * will not be included in the results
     *
     * @param array $fields
     * @return array
     */
    public static function committeeLiaisonData(?array $fields=null)
    {
        $columns = self::getDataColumns();
        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees         c
                join liaisons           l on c.id=l.committee_id
                left join people        p on l.person_id=p.id
                left join people_emails e on e.person_id=p.id and e.main=1
                left join people_phones h on h.person_id=p.id and h.main=1
                $where
                order by c.name";
        return self::performDataSelect($sql, $params);
     }

     /**
      * This query uses a straight join on People
      *
      * If a person is not a liaison, then no data rows will be returned
      *
      * @param array $fields
      * @return array
      */
     public static function personLiaisonData(?array $fields=null)
     {
        $columns = self::getDataColumns();
        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees         c
                join liaisons           l on c.id=l.committee_id
                join people             p on l.person_id=p.id
                left join people_emails e on e.person_id=p.id and e.main=1
                left join people_phones h on h.person_id=p.id and h.main=1
                $where
                order by c.name";
        return self::performDataSelect($sql, $params);
     }

     public static function isLiaison(int $person_id, int $committee_id): bool
     {
        $sql     = 'select id from liaisons where person_id=? and committee_id=?';
        $db = Database::getConnection();
        $result  = $db->query($sql)->execute([$person_id, $committee_id]);
        return count($result) ? true : false;
     }

    public static function hasDepartment(int $department_id, int $liaison_id): bool
    {
        $sql    = "select l.committee_id
                   from liaisons l
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and l.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $liaison_id]);
        return count($result) ? true : false;
    }
}
