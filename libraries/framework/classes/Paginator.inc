<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 *
 * Takes a list and sets up page indexes for it.  Useful for
 * implementing a LimitIterator
 */
class Paginator implements ArrayAccess,SeekableIterator,Countable
{
	private $pageSize;
	private $pages = array();
	private $key = 0;

	public function __construct($list,$pageSize)
	{
		$this->pageSize = $pageSize;
		for($i=0; $i<(count($list)/$this->pageSize); $i++) { $this->pages[] = $i * $this->pageSize; }
	}

	public function getPageSize() { return $this->pageSize; }
	public function getLastIndex() { return count($this->pages)-1; }

	# Array Access section
	public function offsetExists($offset) { return array_key_exists($offset,$this->pages); }
	public function offsetGet($offset) { return $this->pages[$offset]; }
	public function offsetSet($offset,$value) { } # Read-only for now
	public function offsetUnset($offset) { } # Read-only for now


	# Iterator Interface stuff
	public function rewind() { $this->key = 0; }
	public function next() { $this->key++; }
	public function key() { return $this->key; }
	public function valid() { return array_key_exists($this->key,$this->pages); }
	public function current() { return $this->pages[$this->key]; }
	public function seek($index)
	{
		if (isset($this->pages[$index])) { $this->key = $index; }
		else { throw new OutOfBoundsException('Invalid seek position'); }
	}

	public function getIterator() { return $this; }

	# Countable interface section
	public function count() { return count($this->pages); }
}
