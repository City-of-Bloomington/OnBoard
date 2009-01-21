<?php
/**
 * Base class for iterating over database results
 *
 * @copyright Copyright (C) 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class PDOResultIterator implements ArrayAccess,SeekableIterator,Countable
{
	protected $select = '';
	protected $joins = '';
	protected $where = '';
	protected $sort = '';
	protected $limit = '';
	protected $groupBy = '';
	protected $parameters;
	private $sql;

	protected $list = array();
	private $valid = false;
	private $cacheEnabled = true;
	private $cache = array();
	private $key;

	abstract public function find($fields=null,$sort='',$limit=null,$groupBy=null);
	abstract protected function loadResult($key);

	/**
	 * Assembles the full SQL statement and populates the object with the results
	 *
	 * This class uses prepared queries and supports bound parameters.
	 * In the $options array, specify fields and parameter placeholders.  Send the actual data
	 * in the $parameters array.
	 * Make sure the number of paramters matches what you've declared in the options.
	 * Named parameters are the preferred way to be certain.
	 * $options[] = 'fieldname=:val';
	 * $parameters[':val'] = $val;
	 *
	 * @param array $options
	 * @param array $parameters
	 */
	protected function populateList(array $options=null,array $parameters=null)
	{
		$PDO = Database::getConnection();

		// Make sure to clear out any previous list that was created
		$this->list = array();

		if (count($options)) {
			$this->where = ' where '.implode(' and ',$options);
		}
		$orderBy = $this->sort ? "order by {$this->sort}" : '';
		$groupBy = $this->groupBy ? "group by {$this->groupBy}" : '';
		$limit = $this->limit ? 'limit '.$this->limit : '';
		$this->sql = "{$this->select} {$this->joins} {$this->where} $groupBy $orderBy $limit";

		$query = $PDO->prepare($this->sql);
		$this->parameters = $parameters;
		$query->execute($parameters);

		$result = $query->fetchAll();
		if ($result) {
			foreach($result as $row) {
				$this->list[] = $row['id'];
			}
		}
	}

	/**
	 * Splits the results up into pages of results
	 *
	 * @param int $pageSize The number of results per page
	 * @return Paginator
	 */
	public function getPagination($pageSize) {
		return new Paginator($this,$pageSize);
	}


	// Array Access section
	/**
	 * @param int $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset,$this->list);
	}
	/**
	 * Unimplemented stub requried for interface compliance
	 * @ignore
	 */
	public function offsetSet($offset,$value) { } // Read-only for now
	/**
	 * Unimplemented stub requried for interface compliance
	 * @ignore
	 */
	public function offsetUnset($offset) { } // Read-only for now
	/**
	 * @param int $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			if ($this->cacheEnabled) {
				if (!isset($this->cache[$offset])) {
					$this->cache[$offset] = $this->loadResult($offset);
				}
				return $this->cache[$offset];
			}
			else {
				return $this->loadResult($offset);
			}
		}
		else {
			throw new OutOfBoundsException('Invalid seek position');
		}
	}



	// SPLIterator Section
	/**
	 * Reset the pionter to the first element
	 */
	public function rewind() {
		$this->key = 0;
	}
	/**
	 * Advance to the next element
	 */
	public function next() {
		$this->key++;
	}
	/**
	 * Return the index of the current element
	 * @return int
	 */
	public function key() {
		return $this->key;
	}
	/**
	 * @return boolean
	 */
	public function valid() {
		return array_key_exists($this->key,$this->list);
	}
	/**
	 * @return mixed
	 */
	public function current()
	{
		if ($this->cacheEnabled) {
			if (!isset($this->cache[$this->key])) {
				$this->cache[$this->key] = $this->loadResult($this->key);
			}
			return $this->cache[$this->key];
		}
		return $this->loadResult($this->key);
	}
	/**
	 * @param int $index
	 */
	public function seek($index)
	{
		if (isset($this->list[$index])) {
			$this->key = $index;
		}
		else {
			throw new OutOfBoundsException('Invalid seek position');
		}
	}

	/**
	 * @return Iterator
	 */
	public function getIterator()
	{
		return $this;
	}

	// Countable Section
	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->list);
	}

	// Getters
	/**
	 * @return string
	 */
	public function getSelect()
	{
		return $this->select;
	}
	/**
	 * @return string
	 */
	public function getJoins()
	{
		return $this->joins;
	}
	/**
	 * @return string
	 */
	public function getWhere()
	{
		return $this->where;
	}
	/**
	 * @return string
	 */
	public function getGroupBy()
	{
		return $this->groupBy;
	}
	/**
	 * @return string
	 */
	public function getSort()
	{
		return $this->sort;
	}
	/**
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}
	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
	/**
	 * @return string
	 */
	public function getSQL()
	{
		return $this->sql;
	}

	// Cache Enable/Disable functions
	/**
	 * Turns on the caching of loaded objects
	 *
	 * This iterator does one database call to get all the ID from the table,
	 * then, it does a database call for each of the rows returned, in order to load the object
	 * With caching on, the loaded objects will be saved in case you want to iterate over the
	 * result set again
	 */
	public function enableCache()
	{
		$this->cacheEnabled = true;
	}
	/**
	 * Turns off the caching of loaded object
	 *
	 * This iterator does one database call to get all the ID from the table,
	 * then, it does a database call for each of the rows returned, in order to load the object
	 * With caching off you can save a bit of memory by not storing the objects
	 * for future iterations
	 */
	public function disableCache()
	{
		$this->cacheEnabled = false;
	}
}
