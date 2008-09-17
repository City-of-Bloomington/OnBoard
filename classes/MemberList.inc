<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class MemberList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select members.id as id from members';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='term_start desc',$limit=null,$groupBy=null)
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
		if (isset($fields['seat_id']))
		{
			$options[] = 'seat_id=:seat_id';
			$parameters[':seat_id'] = $fields['seat_id'];
		}
		if (isset($fields['user_id']))
		{
			$options[] = 'user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}
		if (isset($fields['term_start']))
		{
			$options[] = 'term_start=:term_start';
			$parameters[':term_start'] = $fields['term_start'];
		}
		if (isset($fields['term_end']))
		{
			$options[] = 'term_end=:term_end';
			$parameters[':term_end'] = $fields['term_end'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new Member($this->list[$key]); }
}
