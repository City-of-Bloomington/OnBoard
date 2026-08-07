<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\Database;
use Application\PdoRepository;

class LiaisonTable extends PdoRepository
{
    public static $valid_filters  = ['committee_id', 'person_id', 'type', 'status', 'current'];
    public static $valid_statuses = ['Staff', 'Non-staff', 'Vacant'];
    public static $valid_types    = ['legal', 'departmental'];

    public function __construct() { parent::__construct('liaisons', __namespace__.'\Liaison'); }


    public static $select = <<<END
    select l.id        as liaison_id,
           c.id        as committee_id,
           c.name      as committee,
           c.code      as committee_code,
           l.type      as type,
           p.id        as person_id,
           p.username  as username,
           p.firstname as firstname,
           p.lastname  as lastname,
           e.email     as email,
           h.number    as phone,
           case when p.id is null then 'Vacant'
                when p.username is not null then 'Staff'
                else 'Non-staff'
           end as status
    from committees c
    END;

    /**
     * Prepares sql for the WHERE and binds values for all values
     */
    private static function bindFields(array &$where, array &$params, array $fields=[])
    {
        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (in_array($k, self::$valid_filters)) {
                    switch ($k) {
                        case 'current':
                            $where[] = '(c.endDate is null or now() < c.endDate)';
                        break;

                        case 'status':
                            $where[] = $v=='Vacant' ? 'p.id is null'
                                     : ($v=='Staff' ? 'p.username is not null' : 'p.username is null');
                        break;

                        default:
                            $where[]    = "l.$k=:$k";
                            $params[$k] = $v;
                    }
                }
            }
        }
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
        $joins  = [
            'left join liaisons      l on c.id=l.committee_id',
            'left join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql(self::$select, $joins, $where, null, 'c.name, l.type');
        return Database::query($sql, $params);
    }

    /**
     * This query uses a straight join on committees
     *
     * If a committee does not have any liaisons, that committee
     * will not be included in the results
     */
    public function committeeLiaisonData(array $fields=[]): array
    {
        $joins  = [
                 'join liaisons      l on c.id=l.committee_id',
            'left join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql(self::$select, $joins, $where, null, 'c.name');
        return Database::query($sql, $params);
     }

     /**
      * This query uses a straight join on People
      *
      * If a person is not a liaison, then no data rows will be returned
      */
     public function personLiaisonData(array $fields=[]): array
     {
        $joins  = [
                 'join liaisons      l on c.id=l.committee_id',
                 'join people        p on l.person_id=p.id',
            'left join people_emails e on e.person_id=p.id and e.main=1',
            'left join people_phones h on h.person_id=p.id and h.main=1'
        ];
        $where  = [];
        $params = [];

        self::bindFields($where, $params, $fields);
        $sql = parent::buildSql(self::$select, $joins, $where, null, 'c.name');
        return Database::query($sql, $params);
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
