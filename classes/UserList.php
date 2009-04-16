<?php
/**
 * A collection class for User objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each User object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * The PDOResultIterator uses prepared queries; it is recommended to use bound
 * parameters for each of the options you handle
 * 
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class UserList extends PDOResultIterator
{
	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select users.id as id from users';
		if (is_array($fields)) $this->find($fields);
	}

	/**
	 * @param array $fields
	 * @param string $sort
	 * @param string $limit
	 * @param string $groupBy
	 */
	public function find($fields=null,$sort='username',$limit=null,$groupBy=null)
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
		if (isset($fields['username'])) {
			$options[] = 'username=:username';
			$parameters[':username'] = $fields['username'];
		}
		if (isset($fields['password'])) {
			$options[] = 'password=:password';
			$parameters[':password'] = $fields['password'];
		}
		if (isset($fields['authenticationMethod'])) {
			$options[] = 'authenticationMethod=:authenticationMethod';
			$parameters[':authenticationMethod'] = $fields['authenticationMethod'];
		}

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		$joins = array();

		if (isset($fields['firstname'])) {
			$joins['peopleJoin'] = 'left join people on users.id=people.user_id';
			$options[] = 'firstname=:firstname';
			$parameters[':firstname'] = $fields['firstname'];
		}
		if (isset($fields['lastname'])) {
			$joins['peopleJoin'] = 'left join people on users.id=people.user_id';
			$options[] = 'lastname=:lastname';
			$parameters[':lastname'] = $fields['lastname'];
		}
		if (isset($fields['email'])) {
			$joins['peopleJoin'] = 'left join people on users.id=people.user_id';
			$options[] = 'email=:email';
			$parameters[':email'] = $fields['email'];
		}
		if (isset($fields['role'])) {
			$joins['roleJoin'] = 'left join user_roles on users.id=user_id left join roles on role_id=roles.id';
			$options[] = 'role=:role';
			$parameters[':role'] = $fields['role'];
		}

		$this->joins = implode(' ',$joins);
		$this->populateList($options,$parameters);
	}

	/**
	 * @param mixed $key
	 */
	protected function loadResult($key)
	{
		return new User($this->list[$key]);
	}
}
