<?php
/**
 * A collection class for Person objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Person object
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
class PersonList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select distinct p.id as id from people p';
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
	public function find($fields=null,$sort='p.lastname,p.firstname',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'p.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['firstname'])) {
			$options[] = 'p.firstname=:firstname';
			$parameters[':firstname'] = $fields['firstname'];
		}

		if (isset($fields['lastname'])) {
			$options[] = 'p.lastname=:lastname';
			$parameters[':lastname'] = $fields['lastname'];
		}

		if (isset($fields['email'])) {
			$options[] = 'p.email=:email';
			$parameters[':email'] = $fields['email'];
		}

		if (isset($fields['address'])) {
			$options[] = 'p.address=:address';
			$parameters[':address'] = $fields['address'];
		}

		if (isset($fields['city'])) {
			$options[] = 'p.city=:city';
			$parameters[':city'] = $fields['city'];
		}

		if (isset($fields['zipcode'])) {
			$options[] = 'p.zipcode=:zipcode';
			$parameters[':zipcode'] = $fields['zipcode'];
		}

		if (isset($fields['about'])) {
			$options[] = 'p.about=:about';
			$parameters[':about'] = $fields['about'];
		}

		if (isset($fields['gender'])) {
			$options[] = 'p.gender=:gender';
			$parameters[':gender'] = $fields['gender'];
		}

		if (isset($fields['race_id'])) {
			$options[] = 'p.race_id=:race_id';
			$parameters[':race_id'] = $fields['race_id'];
		}

		if (isset($fields['birthdate'])) {
			$options[] = 'p.birthdate=:birthdate';
			$parameters[':birthdate'] = $fields['birthdate'];
		}

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['committee_id'])) {
			$this->joins.= ' left join terms t on p.id=t.person_id';
			$this->joins.= ' left join seats s on t.seat_id=s.id';

			if (is_array($fields['committee_id'])) {
				$committees = implode(',',$fields['committee_id']);
				$options[] = "s.committee_id in ($committees)";
			}
			else {
				$options[] = 's.committee_id=:committee_id';
				$parameters[':committee_id'] = $fields['committee_id'];
			}
		}

		if (isset($fields['topicList'])) {
			$this->joins.= "
				left join terms t on p.id=t.person_id
				left join votingRecords vr on t.id=vr.term_id
				left join votes v on vr.vote_id=v.id
			";
			$options[] = "v.topic_id in ({$fields['topicList']->getSQL()})";
			$parameters = array_merge($parameters,$fields['topicList']->getParameters());
		}

		$this->populateList($options,$parameters);
	}

	/**
	 * Populates the collection from the database based on the $fields you handle
	 *
	 * @param array $fields
	 * @param string $sort
	 * @param int $limit
	 * @param string $groupBy
	 */
	public function search($fields=null,$sort='lastname,firstname',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['firstname']) && $fields['firstname']) {
			// Only use the first three letters for the search
			$firstname = substr($fields['firstname'],0,3);
			$options[] = 'firstname like :firstname';
			$parameters[':firstname'] = "$firstname%";
		}

		if (isset($fields['lastname']) && $fields['lastname']) {
			// Only use the first three letters for the search
			$lastname = substr($fields['lastname'],0,3);
			$options[] = 'lastname like :lastname';
			$parameters[':lastname'] = "$lastname%";
		}

		// We want to do an OR search instead of the normal AND
		$options = implode(' or ',$options);
		$options = array("($options)");

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here

		$this->populateList($options,$parameters);
	}

	/**
	 * Loads a single Person object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Person($this->list[$key]);
	}
}
