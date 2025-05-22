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

    private function processFields(array $fields=null, Select &$select)
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

    public function find($fields=null, $order='start', $paginated=false, $limit=null)
    {
        $select = new Select(['m' => self::TABLE]);
        $this->processFields($fields, $select);

        return parent::performSelect($select, $order, $paginated, $limit);
    }

    public function years($fields=null): array
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

        $this->processFields($fields, $select);

        $query  = $sql->prepareStatementForSqlObject($select);
        $result = $query->execute();
        $out    = [];
        foreach ($result as $row) {
            $out[$row['year']] = (int)$row['count'];
        }
        return $out;
    }
}
