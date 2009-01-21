<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class VoteList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select votes.id as id from votes';
		if (is_array($fields)) $this->find($fields);
	}
	

	public function find($fields=null,$sort='date desc',$limit=null,$groupBy=null)
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
		if (isset($fields['date']))
		{
			$options[] = 'date=:date';
			$parameters[':date'] = $fields['date'];
		}
		if (isset($fields['voteType_id']))
		{
			$options[] = 'voteType_id=:voteType_id';
			$parameters[':voteType_id'] = $fields['voteType_id'];
		}
		if (isset($fields['topic_id']))
		{
			$options[] = 'topic_id=:topic_id';
			$parameters[':topic_id'] = $fields['topic_id'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here

		$this->populateList($options,$parameters);
	}
	

	protected function loadResult($key) { return new Vote($this->list[$key]); }
}
