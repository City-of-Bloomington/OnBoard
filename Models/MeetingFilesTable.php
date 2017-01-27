<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class MeetingFilesTable extends TableGateway
{
    const TABLE = 'meetingFiles';
    public static $types = ['Agenda', 'Minutes', 'Packet'];

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

	public static function dates(array $fields=null)
	{
        $select = new Select(self::TABLE);
        $select->quatifier(Select::QUANTIFIER_DISTINCT);
        $select->columns(['meetingDate']);

        $this->processFields($fields, $select);

        return parent::performSelect($select);
	}
}
