<?php
/**
 * @copyright 2007-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PhoneNumberList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select phoneNumbers.id as id from phoneNumbers';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='ordering',$limit=null,$groupBy=null)
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
		if (isset($fields['user_id']))
		{
			$options[] = 'user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}
		if (isset($fields['ordering']))
		{
			$options[] = 'ordering=:ordering';
			$parameters[':ordering'] = $fields['ordering'];
		}
		if (isset($fields['type']))
		{
			$options[] = 'type=:type';
			$parameters[':type'] = $fields['type'];
		}
		if (isset($fields['number']))
		{
			$options[] = 'number=:number';
			$parameters[':number'] = $fields['number'];
		}
		if (isset($fields['private']))
		{
			$options[] = 'private=:private';
			$parameters[':private'] = $fields['private'];
		}


		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new PhoneNumber($this->list[$key]); }
}
