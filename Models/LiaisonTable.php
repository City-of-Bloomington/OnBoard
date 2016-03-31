<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\Database;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class LiaisonTable extends TableGateway
{
	public function __construct() { parent::__construct('liaisons', __namespace__.'\Liaison'); }

    /**
     * These are the fields that will be returned for all *Data functions
     */
    public static $dataFields = [
        'committee_id' => 'c.id',
        'committee'    => 'c.name',
        'department'  => "group_concat(d.name separator '|')",
        'type'         => 'l.type',
        'person_id'    => 'p.id',
        'firstname'    => 'p.firstname',
        'lastname'     => 'p.lastname',
        'email'        => 'p.email',
        'phone'        => 'p.phone'
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
    private static function bindFields($fields=null)
    {
        $where  = [];
        $params = [];
        if (count($fields)) {
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
        $zend_db = Database::getConnection();
        $result = $zend_db->query($sql)->execute($params);
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
	public static function data($fields=null)
	{
        $columns = self::getDataColumns();

        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees c
                left join liaisons l on c.id=l.committee_id
                left join people p on l.person_id=p.id
                left join committee_departments x on c.id=x.committee_id
                left join departments d on x.department_id=d.id
                $where
                group by c.id, p.id
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
	public static function committeeLiaisonData($fields=null)
	{
        $columns = self::getDataColumns();
        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees                 c
                join liaisons                   l on c.id=l.committee_id
                left join people                p on l.person_id=p.id
                left join committee_departments x on c.id=x.committee_id
                left join departments           d on x.department_id=d.id
                $where
                group by c.id, p.id
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
 	public static function personLiaisonData($fields=null)
 	{
        $columns = self::getDataColumns();
        list($where, $params) = self::bindFields($fields);

        $sql = "select $columns
                from committees                 c
                join liaisons                   l on c.id=l.committee_id
                join people                     p on l.person_id=p.id
                left join committee_departments x on c.id=x.committee_id
                left join departments           d on x.department_id=d.id
                $where
                group by c.id, p.id
                order by c.name";
        return self::performDataSelect($sql, $params);
 	}
}