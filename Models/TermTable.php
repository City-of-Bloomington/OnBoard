<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class TermTable extends TableGateway
{
	public function __construct() { parent::__construct('terms', __namespace__.'\Term'); }

	public function find($fields=null, $order='terms.term_start', $paginated=false, $limit=null)
	{
		$select = new Select('terms');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
						$select->where("terms.term_start<='$date'");
						$select->where("(terms.term_end is null or terms.term_end>='$date')");
						break;

					case 'committee_id':
						$select->join(['s'=>'seats'], 'terms.seat_id=s.id', []);
						$select->where(['s.committee_id' => $value]);
						break;

					case 'term_id':
						$order = "p.lastname,p.firstname";
						$select->join(['p' =>'people'],        'terms.person_id=p.id',      [], $select::JOIN_LEFT);
						$select->join(['v1'=>'votingRecords'], 'terms.id=v1.term_id',       [], $select::JOIN_INNER);
						$select->join(['v2'=>'votingRecords'], 'v1.vote_id=v2.vote_id', [], $select::JOIN_INNER);

						$value = (int)$value;
						$select->where("v2.term_id=$value");
						$select->where("v1.term_id!=$value");
						break;

					case 'topicList':
						//TODO List of terms for any given set of Topics
						// topicList used to provide a TopicList, but now, it provides
						// a Zend\Db\ResultSet
						throw new \Exception('queryNotImplemented');
						/*
						$this->joins.= "
							left join votingRecords r on t.id=r.term_id
							left join votes v on r.vote_id=v.id
						";

						$options[] = "v.topic_id in ({$fields['topicList']->getSQL()})";
						$parameters = array_merge($parameters,$fields['topicList']->getParameters());
						*/
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
