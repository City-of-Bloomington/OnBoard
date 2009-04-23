<?php
/**
 * A collection class for Topic objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Topic object
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
class TopicList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select distinct topics.id as id from topics';
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
		$this->sort = 'topics.'.$sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'topics.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['topicType_id'])) {
			$options[] = 'topics.topicType_id=:topicType_id';
			$parameters[':topicType_id'] = $fields['topicType_id'];
		}

		if (isset($fields['date'])) {
			$options[] = 'topics.date=:date';
			$parameters[':date'] = $fields['date'];
		}

		if (isset($fields['year'])) {
			$options[] = 'year(topics.date)=:year';
			$parameters[':year'] = $fields['year'];
		}

		if (isset($fields['number'])) {
			$options[] = 'topics.number=:number';
			$parameters[':number'] = $fields['number'];
		}

		if (isset($fields['description'])) {
			$options[] = 'topics.description=:description';
			$parameters[':description'] = $fields['description'];
		}

		if (isset($fields['synopsis'])) {
			$options[] = 'topics.synopsis=:synopsis';
			$parameters[':synopsis'] = $fields['synopsis'];
		}

		if (isset($fields['committee_id'])) {
			$options[] = 'topics.committee_id=:committee_id';
			$parameters[':committee_id'] = $fields['committee_id'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['tag'])) {
			$fields['tag_id'] = $fields['tag']->getId();
		}

		if (isset($fields['tag_id'])) {
			$this->joins.= ' left join topic_tags t on topics.id=t.topic_id';
			$options[] = 'tag_id=:tag_id';
			$parameters[':tag_id'] = $fields['tag_id'];
		}

		if (isset($fields['person_id'])) {
			$this->joins.= "
				left join votes v on topics.id=v.topic_id
				left join votingRecords vr on vr.vote_id=v.id
				left join terms on vr.term_id=terms.id";
			$options[] = 'terms.person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}

		$this->populateList($options,$parameters);
	}

	/**
	 * Loads a single Topic object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Topic($this->list[$key]);
	}

	/**
	 * Returns all the tags that match any of the topics in this list
	 *
	 * @return TagList
	 */
	public function getTags()
	{
		if (count($this)) {
			return new TagList(array('topicList'=>$this));
		}
		return array();
	}

	/**
	 * Returns the terms for the people who participated in any vote for any of these topics
	 *
	 * @return TermList
	 */
	public function getTerms()
	{
		return new TermList(array('topicList'=>$this));
	}

	/**
	 * Returns all the people who voted on topics in this list
	 *
	 * @return PeopleList
	 */
	public function getPeople()
	{
		if(count($this)) {
			return new PersonList(array('topicList'=>$this));
		}
	}

	/**
	 * Returns a list of years for this collection.
	 * If this collection is not populated, it returns a list of all the years in the database
	 *
	 * @return array
	 */
	public function getYears()
	{
		$pdo = Database::getConnection();

		if ($this->getSQL()) {
			$sql = str_replace($this->getSelect(),
							   'select distinct year(date) as year from topics',
							   $this->getSQL());
			$query = $pdo->prepare($sql);
			$query->execute($this->getParameters());
		}
		else {
			$sql = 'select distinct year(date) as year from topics order by date desc';
			$query = $pdo->prepare($sql);
			$query->execute();
		}
		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		$years = array();
		foreach ($result as $row) {
			$years[] = $row['year'];
		}
		return $years;
	}
}
