<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class TypesTable extends TableGateway
{
    private $columns = ['id', 'name'];

	public function __construct() { parent::__construct('legislationTypes', __namespace__.'\Type'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('legislationTypes');

		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'subtype':
                        $select->where(['subtype'=>$value ? 1 : 0]);
                    break;

                    default:
                        if (in_array($key, $this->columns)) {
                            $select->where([$key=>$value]);
                        }

				}
            }
        }

		return parent::performSelect($select, $order, $paginated, $limit);
    }
}
