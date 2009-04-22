<?php
/**
 * A collection class for Appointer objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Appointer object
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
class AppointerList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select distinct a.id as id from appointers a';
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
	public function find($fields=null,$sort='a.name',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = array();

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'a.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['name'])) {
			$options[] = 'a.name=:name';
			$parameters[':name'] = $fields['name'];
		}

		if (isset($fields['committee_id'])) {
			$this->joins['seat_join'] = 'left join seats on a.id=seats.appointer_id';
			$options[] = 'committee_id=:committee_id';
			$parameters[':committee_id'] = $fields['committee_id'];
		}

		if (isset($fields['person_id'])) {
			$this->joins['seat_join'] = 'left join seats on a.id=seats.appointer_id';
			$this->joins['term_join'] = 'left join terms on seats.id=terms.seat_id';
			$options[] = 'person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		$this->joins = implode(' ',$this->joins);
		$this->populateList($options,$parameters);
	}

	/**
	 * Loads a single Appointer object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Appointer($this->list[$key]);
	}
}
