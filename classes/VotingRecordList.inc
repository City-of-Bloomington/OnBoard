<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class VotingRecordList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select vr.id as id from votingRecords vr';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='v.date desc',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = 'left join votes v on vr.vote_id=v.id';

		$options = array();
		$parameters = array();
		if (isset($fields['id']))
		{
			$options[] = 'vr.id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['member_id']))
		{
			$options[] = 'vr.member_id=:member_id';
			$parameters[':member_id'] = $fields['member_id'];
		}
		if (isset($fields['vote_id']))
		{
			$options[] = 'vr.vote_id=:vote_id';
			$parameters[':vote_id'] = $fields['vote_id'];
		}
		if (isset($fields['memberVote']))
		{
			$options[] = 'vr.memberVote=:memberVote';
			$parameters[':memberVote'] = $fields['memberVote'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here
		if (isset($fields['user_id']))
		{
			$this->joins.= ' left join members m on vr.member_id=m.id';
			$options[] = 'm.user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new VotingRecord($this->list[$key]); }
}
