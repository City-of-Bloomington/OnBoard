<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class VoteTable extends TableGateway
{
	public function __construct() { parent::__construct('votes', __namespace__.'\Vote'); }

	public function find($fields=null, $order='date desc', $paginated=false, $limit=null)
	{
		if (!$order) { $order = 'date desc'; }
		
		$select = new Select('votes');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'committee_id':
						$select->join('topics', 'votes.topic_id=topics.id', []);
						$select->where(['topics.committee_id'=>$value]);
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
