<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
*/
class RoleList extends PDOResultIterator
{
	/**
	 * Roles are really just an attribute array for users.
	 * For now, I'm not loading them in as objects.
	 * This is an optimization to save on database calls
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select name as id from roles';
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * @param array $fields
	 * @param string $sort
	 * @param string $limit
	 * @param string $groupBy
	 */
	public function find($fields=null,$sort='name',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();
		if (isset($fields['id'])) {
			$options[] = 'id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['name'])) {
			$options[] = 'name=:name';
			$parameters[':name'] = $fields['name'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here

		$this->populateList($options,$parameters);
	}

	/**
	 * For now we are not returning Role objects, only the names of each role
	 * @param mixed $key
	 */
	protected function loadResult($key)
	{
		return $this->list[$key];
	}
}
