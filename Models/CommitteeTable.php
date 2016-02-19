<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class CommitteeTable extends TableGateway
{
	public function __construct() { parent::__construct('committees', __namespace__.'\Committee'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('committees');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'member_id':
						$select->join(['m'=>'members'], 'committees.id=m.committee_id', []);
						$select->where(['m.person_id' => $value]);
					break;

					case 'liaison_id':
                        $select->join(['l'=>'committee_liaisons'], 'committees.id=l.committee_id', []);
                        $select->where(['l.person_id' => $value]);
					break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
