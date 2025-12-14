<?php
/**
 * @copyright 2017-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class MeetingFilesTable extends TableGateway
{
    const TABLE = 'meetingFiles';
    public static $sortableFields = ['filename', 'start', 'created'];

    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\MeetingFile'); }

    private function processFields(Select &$select, ?array $fields=null)
    {
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'start':
                        $select->where(['m.start >= ?'=> $value->format('Y-m-d')]);
                    break;

                    case 'end':
                        $select->where(['m.start <= ?'=>$value->format('Y-m-d')]);
                    break;

                    case 'year':
                        $select->where(['year(m.start)=?' => (int)$value]);
                    break;

                    case 'indexed':
                        if ($value) {
                            $select->where(['f.indexed>f.updated']);
                        }
                        else {
                            $select->where(['f.indexed is null or f.updated>f.indexed']);
                        }
                    break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
    }

    public function find(?array $fields=null, string|array|null $order='f.updated desc', ?bool $paginated=false, ?int $limit=null)
    {
        $select = new Select(['f' => self::TABLE]);
        $select->join(['m'=>'meetings'], 'm.id=f.meeting_id', []);
        $this->processFields($select, $fields);

        return parent::performSelect($select, $order, $paginated, $limit);
    }

    public function years(?array $fields=null)
    {
        $sql    = new Sql(Database::getConnection());
        $select = $sql->select()
                      ->from(['f'=>self::TABLE])
                      ->join(['m'=>'meetings'], 'm.id=f.meeting_id', [])
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

    /**
     * Check if a meetingFile has a given department
     */
    public static function hasDepartment(int $department_id, int $file_id): bool
    {
        $sql    = "select d.department_id
                   from meetingFiles          f
                   join meetings              m on m.id=f.meeting_id
                   join committee_departments d on m.committee_id=d.committee_id
                   where d.department_id=? and f.id=?;";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $file_id]);
        return count($result) ? true : false;
    }
}
