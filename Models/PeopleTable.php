<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class PeopleTable extends TableGateway
{
	public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

	public function find($fields=null, $order='lastname', $paginated=false, $limit=null)
	{
		$select = new Select('people');
		$select->quantifier(Select::QUANTIFIER_DISTINCT);

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'user_account':
						if ($value) {
							$select->where('username is not null');
						}
						else {
							$select->where('username is null');
						}
					break;

					case 'committee_id':
						$select->join(['t'=>'terms'], 'people.id=t.person_id', []);
						$select->join(['s'=>'seats'], 't.seat_id=s.id',        []);
						$select->where(['s.committee_id'=>$value]);
						break;

					case 'topicList':
						//TODO List of people for any given set of Topics
						// topicList used to provide a TopicList, but now, it provides
						// a Zend\Db\ResultSet
						throw new \Exception('queryNotImplemented');
						/*
						$this->joins.= "
							left join terms t on p.id=t.person_id
							left join votingRecords vr on t.id=vr.term_id
							left join votes v on vr.vote_id=v.id
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
