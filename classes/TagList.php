<?php
/**
 * A collection class for Tag objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Tag object
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
class TagList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select tags.id as id from tags';
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
	public function find($fields=null,$sort='name',$limit=null,$groupBy=null)
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

		if (isset($fields['name'])) {
			$options[] = 'name=:name';
			$parameters[':name'] = $fields['name'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['topic_id']))
		{
			$this->joins.= ' left join topic_tags t on tags.id=t.tag_id';
			$options[] = 'topic_id=:topic_id';
			$parameters[':topic_id'] = $fields['topic_id'];
		}

		if (isset($fields['topicList'])) {
			$topics = $fields['topicList']->getSQL();

			$this->joins.= ' join topic_tags linked on tags.id=linked.tag_id';
			$this->joins.= " join ($topics)related_topics on linked.topic_id=related_topics.id";
			$parameters = array_merge($parameters,$fields['topicList']->getParameters());
		}

		$this->populateList($options,$parameters);
	}


	/**
	 * Loads a single Tag object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Tag($this->list[$key]);
	}
}
