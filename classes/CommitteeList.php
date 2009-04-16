<?php
/**
 * A collection class for Committee objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Committee object
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
class CommitteeList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select distinct c.id as id from committees c';
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
	public function find($fields=null,$sort='c.name',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'c.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['name'])) {
			$options[] = 'c.name=:name';
			$parameters[':name'] = $fields['name'];
		}

		if (isset($fields['statutoryName'])) {
			$options[] = 'c.statutoryName=:statutoryName';
			$parameters[':statutoryName'] = $fields['statutoryName'];
		}

		if (isset($fields['statuteReference'])) {
			$options[] = 'c.statuteReference=:statuteReference';
			$parameters[':statuteReference'] = $fields['statuteReference'];
		}

		if (isset($fields['dateFormed'])) {
			$options[] = 'c.dateFormed=:dateFormed';
			$parameters[':dateFormed'] = $fields['dateFormed'];
		}

		if (isset($fields['website'])) {
			$options[] = 'c.website=:website';
			$parameters[':website'] = $fields['website'];
		}

		if (isset($fields['description'])) {
			$options[] = 'c.description=:description';
			$parameters[':description'] = $fields['description'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['person_id'])) {
			$this->joins.= " left join seats s on c.id=s.committee_id";
			$this->joins.= " left join terms t on s.id=t.seat_id";
			$options[] = 't.person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}

		$this->populateList($options,$parameters);
	}


	/**
	 * Loads a single Committee object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Committee($this->list[$key]);
	}
}
