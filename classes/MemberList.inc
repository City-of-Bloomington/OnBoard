<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class MemberList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select m.id as id from members m ';
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
			$options[] = 'm.id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['seat_id']))
		{
			$options[] = 'm.seat_id=:seat_id';
			$parameters[':seat_id'] = $fields['seat_id'];
		}
		if (isset($fields['user_id']))
		{
			$options[] = 'm.user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}
		if (isset($fields['term_start']))
		{
			$options[] = 'm.term_start=:term_start';
			$parameters[':term_start'] = $fields['term_start'];
		}
		if (isset($fields['term_end']))
		{
			$options[] = 'm.term_end=:term_end';
			$parameters[':term_end'] = $fields['term_end'];
		}

		if (isset($fields['status'])=='current')
		{
			$options[] = 'm.term_start<now()';
			$options[] = '(m.term_end is null or m.term_end>now())';
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here
		if (isset($fields['committee_id']))
		{
			$this->joins.=" left join seats s on m.seat_id=s.id ";
			$options[] = 's.committee_id=:committee_id';
			$parameters[':committee_id'] = $fields['committee_id'];
		}
		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new Member($this->list[$key]); }
}
