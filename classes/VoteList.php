<?php
/**
 * A collection class for Vote objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Vote object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * The PDOResultIterator uses prepared queries; it is recommended to use bound
 * parameters for each of the options you handle
 *
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class VoteList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select votes.id as id from votes';
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection from the database based on the $fields you handle
	 *
	 * @param array $fields
	 * @param string $sort
	 * @param int $limit
	 * @param string $groupBy
	 */
	public function find($fields=null,$sort='date desc',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['date'])) {
			$options[] = 'date=:date';
			$parameters[':date'] = $fields['date'];
		}

		if (isset($fields['voteType_id'])) {
			$options[] = 'voteType_id=:voteType_id';
			$parameters[':voteType_id'] = $fields['voteType_id'];
		}

		if (isset($fields['topic_id'])) {
			$options[] = 'topic_id=:topic_id';
			$parameters[':topic_id'] = $fields['topic_id'];
		}

		if (isset($fields['outcome'])) {
			$options[] = 'outcome=:outcome';
			$parameters[':outcome'] = $fields['outcome'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here

		$this->populateList($options,$parameters);
	}


	/**
	 * Loads a single Vote object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Vote($this->list[$key]);
	}
}
