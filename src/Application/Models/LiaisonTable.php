<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class LiaisonTable extends PdoRepository
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
     */
    private static function bindFields(array &$where, array &$params, array $fields=[])
    {
        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (array_key_exists($k, self::$dataFields)) {
                    $f          = self::$dataFields[$k];
                    $where[]    = "$f=:$k";
                    $params[$k] = $v;
                }
                elseif ($k === 'current' && $v) {
                    $where[] = '(c.endDate is null or now() < c.endDate)';
                }
            }
        }
    }

    private function performDataSelect(string $select, array $params): array
    {
        $qq    = $this->pdo->prepare($select);
        $qq->execute($params);
        $res   = $qq->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'fields'  => array_keys(self::$dataFields),
            'results' => $res
        ];
    }

    /**
     * Returns liaison data for all committees
     *
     * This query does a left joins of liaisons for committees.
     * So, all committees will be represented, but there may be
     * empty fields for the liaison and person information.
     */
    public function data(array $fields=[]): array
    {
        $columns = self::getDataColumns();

        $select = "select $columns from committees c";
        $joins  = [
            'left join liaisons      l on c.id=l.committee_id',
            'left join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql($select, $joins, $where, null, 'c.name');
        return $this->performDataSelect($sql, $params);
    }

    /**
     * This query uses a straight join on committees
     *
     * If a committee does not have any liaisons, that committee
     * will not be included in the results
     */
    public function committeeLiaisonData(array $fields=[]): array
    {
        $columns = self::getDataColumns();

        $select = "select $columns from committees c";
        $joins  = [
                 'join liaisons      l on c.id=l.committee_id',
            'left join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql($select, $joins, $where, null, 'c.name');
        return $this->performDataSelect($sql, $params);
     }

     /**
      * This query uses a straight join on People
      *
      * If a person is not a liaison, then no data rows will be returned
      */
     public function personLiaisonData(array $fields=[]): array
     {
        $columns = self::getDataColumns();

        $select = "select $columns from committees c";
        $joins  = [
                 'join liaisons      l on c.id=l.committee_id',
                 'join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql($select, $joins, $where, null, 'c.name');
        return $this->performDataSelect($sql, $params);
     }

     public function isLiaison(int $person_id, int $committee_id): bool
     {
        $sql    = 'select id from liaisons where person_id=? and committee_id=?';
        $q      = $this->pdo->prepare($sql);
        $q->execute([$person_id, $committee_id]);
        $result = $q->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
     }

    public function hasDepartment(int $department_id, int $liaison_id): bool
    {
        $sql    = "select l.committee_id
                   from liaisons l
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and l.id=?";
        $q      = $this->pdo->prepare($sql);
        $q->execute([$department_id, $liaison_id]);
        $result = $q->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
