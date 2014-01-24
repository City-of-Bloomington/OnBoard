<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class VotingRecordTable extends TableGateway
{
	public function __construct() { parent::__construct('votingRecords', __namespace__.'\VotingRecord'); }

	public function find($fields=null, $order='votes.date desc,people.lastname', $paginated=false, $limit=null)
	{
		$select = new Select('votingRecords');
		$select->quantifier(Select::QUANTIFIER_DISTINCT);
		$select->join('votes',  'votingRecords.vote_id=votes.id', [], Select::JOIN_LEFT);
		$select->join('terms',  'votingRecords.term_id=terms.id', [], Select::JOIN_LEFT);
		$select->join('people', 'terms.person_id=people.id',      [], Select::JOIN_LEFT);

		foreach ($fields as $key=>$value) {
			switch ($key) {
				case 'person_id':
					$select->where(['terms.person_id'=>$value]);
					break;

				case 'voteType_id':
					$select->where(['votes.voteType_id'=>$value]);
					break;

				case 'topicType_id':
					$select->join('topics', 'votes.topic_id=topics.id', []);
					$select->where(['topics.topicType_id'=>$value]);
					break;

				default:
					$select->where(["votingRecords.$key"=>$value]);
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
