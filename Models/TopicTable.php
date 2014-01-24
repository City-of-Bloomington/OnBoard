<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\Database;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class TopicTable extends TableGateway
{
	public function __construct() { parent::__construct('topics', __namespace__.'\Topic'); }

	public function find($fields=null, $order='date desc', $paginated=false, $limit=null)
	{
		$select = new Select('topics');
		if (count($fields)) {
			$this->handleFields($select, $fields);
		}

		if (!in_array($order, ['date', 'date desc', 'number', 'number desc'])) {
			$order = 'date desc';
		}
		$order = 'topics.'.$order;
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	/**
	 * Returns a list of years
	 *
	 * @param array $fields The same fields you would send to ->find()
	 */
	public function getYears($fields=null)
	{
		$sql = new Sql(Database::getConnection());

		$select = $sql->select('topics')
					  ->quantifier(Select::QUANTIFIER_DISTINCT)
					  ->columns(['year'=>new Expression('year(date)')])
					  ->order('date desc');
		if ($fields) {
			$this->handleFields($select, $fields);
		}

		$result = $sql->prepareStatementForSqlObject($select)->execute();

		$years = [];
		foreach ($result as $row) {
			$years[] = $row['year'];
		}
		return $years;
	}

	private function handleFields(Select &$select, $fields)
	{
		foreach ($fields as $key=>$value) {
			switch ($key) {
				case 'tag':
					try {
						$tag = new Tag($value);
						$value = $tag->getId();
					}
					catch (\Exception $e) {
						// Just ignore invalid tags
					}
					// Intentional Fall-through
				case 'tag_id':
					$select->join(['t'=>'topic_tags'], 'topics.id=t.topic_id', []);
					$select->where(['t.tag_id'=>$value]);
					break;

				case 'tags':
					// Used for creating tag clouds.
					// We want only topics that have all of the tags provided
					$select->join(['t'=>'topic_tags'], 'topics.id=t.topic_id', []);
					foreach ($value as $t) {
						try {
							$tag = new Tag($t);
							$select->where(['t.tag_id'=>$tag->getId()]);
						}
						catch (\Exception $e) {
							// Just ignore invalid tags
						}
					}
					break;

				case 'person_id':
					$select->join(['v'=>'votes'], 'topics.id=v.topic_id', []);
					$select->join(['vr'=>'votingRecords'], 'v.id=vr.vote_id', []);
					$select->join('terms', 'vr.term_id=terms.id', []);
					$select->where([$key=>$value]);
					break;

				case 'year':
					$value = (int)$value;
					$select->where("year(topics.date)=$value");
					break;

				default:
					$select->where([$key=>$value]);
			}
		}
	}
}
