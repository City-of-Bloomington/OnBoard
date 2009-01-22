<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class CommitteeList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select committees.id as id from committees';
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
		if (isset($fields['statutoryName']))
		{
			$options[] = 'statutoryName=:statutoryName';
			$parameters[':statutoryName'] = $fields['statutoryName'];
		}
		if (isset($fields['statuteReference']))
		{
			$options[] = 'statuteReference=:statuteReference';
			$parameters[':statuteReference'] = $fields['statuteReference'];
		}
		if (isset($fields['dateFormed']))
		{
			$options[] = 'dateFormed=:dateFormed';
			$parameters[':dateFormed'] = $fields['dateFormed'];
		}
		if (isset($fields['website']))
		{
			$options[] = 'website=:website';
			$parameters[':website'] = $fields['website'];
		}
		if (isset($fields['description']))
		{
			$options[] = 'description=:description';
			$parameters[':description'] = $fields['description'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new Committee($this->list[$key]); }
}
