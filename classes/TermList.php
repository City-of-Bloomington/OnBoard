<?php
/**
 * A collection class for Term objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Term object
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
class TermList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select distinct t.id as id from terms t';
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
	public function find($fields=null,$sort='t.term_start desc',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 't.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['seat_id'])) {
			$options[] = 't.seat_id=:seat_id';
			$parameters[':seat_id'] = $fields['seat_id'];
		}

		if (isset($fields['person_id'])) {
			$options[] = 't.person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}

		if (isset($fields['term_start'])) {
			$options[] = 't.term_start=:term_start';
			$parameters[':term_start'] = $fields['term_start'];
		}

		if (isset($fields['term_end'])) {
			$options[] = 't.term_end=:term_end';
			$parameters[':term_end'] = $fields['term_end'];
		}

		if (isset($fields['current'])) {
			$date = date('Y-m-d H:i:s',$fields['current']);

			$options[] = 't.term_start<:currentStart';
			$options[] = '(t.term_end is null or t.term_end>:currentEnd)';

			$parameters[':currentStart'] = $date;
			$parameters[':currentEnd'] = $date;
		}

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['committee_id'])) {
			$this->joins.= ' left join seats s on t.seat_id=s.id';
			$options[] = 's.committee_id=:committee_id';
			$parameters[':committee_id'] = $fields['committee_id'];
		}

		/*
		*  Find the list of terms of people who voted with the given term
		*/
		if (isset($fields['term_id'])) {
			$this->sort = "p.lastname,p.firstname";
			$this->joins.= "
				left join users p on t.person_id=p.id
				inner join votingRecords v1 on t.id=v1.term_id
				inner join votingRecords v2 on v1.vote_id=v2.vote_id
			";

			$options[] = 'v2.term_id=:term_idA';
			$options[] = 'v1.term_id!=:term_idB';
			$parameters[':term_idA'] = $fields['term_id'];
			$parameters[':term_idB'] = $fields['term_id'];
		}

		/**
		 * List of terms for any given set of Topics
		 * @param TopicList $fields[topicList]
		 */
		if (isset($fields['topicList'])) {
			$this->joins.= "
				left join votingRecords r on t.id=r.term_id
				left join votes v on r.vote_id=v.id
			";

			$options[] = "v.topic_id in ({$fields['topicList']->getSQL()})";
			$parameters = array_merge($parameters,$fields['topicList']->getParameters());
		}

		$this->populateList($options,$parameters);
	}


	/**
	 * Loads a single Term object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Term($this->list[$key]);
	}
}
