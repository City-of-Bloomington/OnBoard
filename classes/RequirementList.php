<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class RequirementList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select requirements.id as id from requirements';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='text',$limit=null,$groupBy=null)
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
		if (isset($fields['text']))
		{
			$options[] = 'text=:text';
			$parameters[':text'] = $fields['text'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['seat_id']))
		{
			$this->joins.= ' left join seat_requirements on requirements.id=requirement_id';
			$options[] = 'seat_id=:seat_id';
			$parameters[':seat_id'] = $fields['seat_id'];
		}

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new Requirement($this->list[$key]); }
}
