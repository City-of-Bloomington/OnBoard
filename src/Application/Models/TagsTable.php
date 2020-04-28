<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\TableGateway;
use Zend\Db\Sql\Select;

class TagsTable extends TableGateway
{
    protected $columns = ['id', 'name'];

	public function __construct() { parent::__construct('tags', __namespace__.'\Tag'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('tags');
		$select->quantifier(Select::QUANTIFIER_DISTINCT);

		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'legislation_id':
                        $select->join(['l'=>'legislation_tags'], 'tags.id=l.legislation_id', []);
                        $select->where(['l.legislation_id'=>$value]);
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
