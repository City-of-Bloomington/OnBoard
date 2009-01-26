<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class TagList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select tags.id as id from tags';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='name',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();
		if (isset($fields['id']))
		{
			$options[] = 'id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['name']))
		{
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


	protected function loadResult($key) { return new Tag($this->list[$key]); }
}
