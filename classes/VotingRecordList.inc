<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class VotingRecordList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select votingRecords.id as id from votingRecords';
		if (is_array($fields)) $this->find($fields);
	}
	

	public function find($fields=null,$sort='id',$limit=null,$groupBy=null)
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
		if (isset($fields['member_id']))
		{
			$options[] = 'member_id=:member_id';
			$parameters[':member_id'] = $fields['member_id'];
		}
		if (isset($fields['vote']))
		{
			$options[] = 'vote=:vote';
			$parameters[':vote'] = $fields['vote'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here

		$this->populateList($options,$parameters);
	}
	

	protected function loadResult($key) { return new VotingRecord($this->list[$key]); }
}
