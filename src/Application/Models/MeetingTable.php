<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class MeetingTable extends TableGateway
{
    const TABLE = 'meetings';
    public static $sortableFields = ['start'];

    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Meeting'); }

    private function processFields(Select &$select, ?array $fields=null)
    {
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'start':
                        $select->where(['m.start >= ?'=> $value->format('Y-m-d H:i:s')]);
                        break;

                    case 'end':
                        $select->where(['m.end <= ?'=>$value->format('Y-m-d H:i:s')]);
                        break;

                    case 'year':
                        $select->where(['year(m.start)=?' => (int)$value]);
                        break;

                    case 'fileType':
                        $select->join(['f'=>'meetingFiles'], 'm.id=f.meeting_id', []);
                        $select->where(['f.type'=>$value]);
                        break;

                    default:
                        $select->where(["m.$key"=>$value]);
                }
            }
        }
    }

    public function find(?array $fields=null, string|array|null $order='start', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = new Select(['m' => self::TABLE]);
        $this->processFields($select, $fields);

        return parent::performSelect($select, $order, $itemsPerPage, $currentPage);
    }

    public function years(?array $fields=null): array
    {
        $sql    = new Sql(Database::getConnection());
        $select = $sql->select()
                      ->from(['m' => self::TABLE])
                      ->columns([
                          'year'  => new Expression('distinct(year(m.start))'),
                          'count' => new Expression('count(*)')
                      ])
                      ->group('year')
                      ->order('year desc');

        $this->processFields($select, $fields);

        $query  = $sql->prepareStatementForSqlObject($select);
        $result = $query->execute();
        $out    = [];
        foreach ($result as $row) {
            $out[$row['year']] = (int)$row['count'];
        }
        return $out;
    }

    public static function hasDepartment(int $department_id, int $meeting_id): bool
    {
        $sql    = "select m.committee_id
                   from meetings m
                   join committee_departments d on m.committee_id=d.committee_id
                   where d.department_id=? and m.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $meeting_id]);
        return count($result) ? true : false;
    }
}
