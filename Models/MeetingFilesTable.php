<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\Database;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class MeetingFilesTable extends TableGateway
{
    const TABLE = 'meetingFiles';
    public static $types          = ['Agenda', 'Minutes', 'Packet'];
    public static $sortableFields = ['filename', 'meetingDate', 'created'];

	public function __construct() { parent::__construct(self::TABLE, __namespace__.'\MeetingFile'); }

	private function processFields(array $fields=null, Select &$select)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'start':
                        $select->where(['meetingDate >= ?'=> $value->format('Y-m-d')]);
                    break;

                    case 'end':
                        $select->where(['meetingDate <= ?'=>$value->format('Y-m-d')]);
                    break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
	}

	public function find($fields=null, $order='updated desc', $paginated=false, $limit=null)
	{
		$select = new Select(self::TABLE);
		$this->processFields($fields, $select);

		return parent::performSelect($select, $order, $paginated, $limit);
	}

	public function years($fields=null)
	{
        $sql    = new Sql(Database::getConnection());
        $select = $sql->select()
                      ->from(self::TABLE)
                      ->columns([new Expression('distinct(year(meetingDate)) as year')])
                      ->order('year desc');

        $this->processFields($fields, $select);

        $query  = $sql->prepareStatementForSqlObject($select);
        $result = $query->execute();
        $out    = [];
        foreach ($result as $row) {
            $out[] = (int)$row['year'];
        }
        return $out;
	}
}
