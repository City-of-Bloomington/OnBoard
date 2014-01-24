<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class TagTable extends TableGateway
{
	public function __construct() { parent::__construct('tags', __namespace__.'\Tag'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('tags');
		if (count($fields)) {
			$this->handleJoins($select, $fields);

			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'topic_id':
						$select->where(['tt.topic_id'=>$value]);
						break;

					case 'committee_id':
						$select->where(['t.committee_id'=>$value]);
						break;

					case 'tags':
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	/**
	 * Run through all the possible joins, so we don't have duplicates
	 */
	private function handleJoins(&$select, &$fields)
	{
		foreach ($fields as $key=>$value) {
			switch ($key) {
				case 'topic_id':
					$joins['tt'] = ['table'=>'topic_tags', 'on'=>'tags.id=tt.tag_id'];
					break;

				case 'committee_id':
					$joins['tt'] = ['table'=>'topic_tags', 'on'=>'tags.id=tt.tag_id'];
					$joins['t' ] = ['table'=>'topics',     'on'=>'tt.topic_id=t.id' ];
					$select->where(['t.committee_id'=>$value]);
					break;

				case 'tags':
					break;
			}
		}
		foreach ($joins as $alias=>$j) {
			$select->join([$alias=>$j['table']], $j['on'], []);
		}
	}
}
