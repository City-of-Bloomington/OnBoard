<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class UserList extends PDOResultIterator
{
	public function __construct($fields=null)
	{
		$this->select = 'select users.id as id from users';
		if (is_array($fields)) $this->find($fields);
	}

	public function find($fields=null,$sort='username',$limit=null,$groupBy=null)
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
		if (isset($fields['username']))
		{
			$options[] = 'username=:username';
			$parameters[':username'] = $fields['username'];
		}
		if (isset($fields['password']))
		{
			$options[] = 'password=:password';
			$parameters[':password'] = $fields['password'];
		}
		if (isset($fields['authenticationMethod']))
		{
			$options[] = 'authenticationMethod=:authenticationMethod';
			$parameters[':authenticationMethod'] = $fields['authenticationMethod'];
		}
		if (isset($fields['firstname']))
		{
			$options[] = 'firstname=:firstname';
			$parameters[':firstname'] = $fields['firstname'];
		}
		if (isset($fields['lastname']))
		{
			$options[] = 'lastname=:lastname';
			$parameters[':lastname'] = $fields['lastname'];
		}
		if (isset($fields['email']))
		{
			$options[] = 'email=:email';
			$parameters[':email'] = $fields['email'];
		}

		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to $options by adding the join SQL
		# to $this->joins here
		if (isset($fields['role']))
		{
			$this->joins.= ' left join user_roles on users.id=user_id left join roles on role_id=roles.id';
			$options[] = 'role=:role';
			$parameters[':role'] = $fields['role'];
		}

		$this->populateList($options,$parameters);
	}

	protected function loadResult($key) { return new User($this->list[$key]); }
}
