<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class SeatList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select seats.id as id from seats';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='title',$limit=null,$groupBy=null)
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
		if (isset($fields['title']))
		{
			$options[] = 'title=:title';
			$parameters[':title'] = $fields['title'];
		}
		if (isset($fields['requirements']))
		{
			$options[] = 'requirements=:requirements';
			$parameters[':requirements'] = $fields['requirements'];
		}
		if (isset($fields['committee_id']))
		{
			$options[] = 'committee_id=:committee_id';
			$parameters[':committee_id'] = $fields['committee_id'];
		}
		if (isset($fields['appointer_id']))
		{
			$options[] = 'appointer_id=:appointer_id';
			$parameters[':appointer_id'] = $fields['appointer_id'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new Seat($this->list[$key]); }
}
