<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class MemberTable extends TableGateway
{
	public function __construct() { parent::__construct('members', __namespace__.'\Member'); }

	public function find($fields=null, $order='startDate desc', $paginated=false, $limit=null)
	{
		$select = new Select('members');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
                        if ($value) {
                            $select->where("startDate <= now()");
                            $select->where("(endDate is null or endDate >= now())");
                        }
                        else {
                            // current == false (the past)
                            $select->where("(endDate is not null and endDate <= now())");
                        }
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
