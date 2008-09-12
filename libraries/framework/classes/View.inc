<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class View
{
	protected $vars = array();

	abstract public function render();

	#----------------------------------------------------------------------------
	# These functions allow direct assigning of variables to the block
	#----------------------------------------------------------------------------
	public function __set($key,$value) { $this->vars[$key] = $value; }
	public function __get($key)
	{
		if (isset($this->vars[$key])) { return $this->vars[$key]; }
		else return null;
	}
	public function __isset($key) { return array_key_exists($key,$this->vars); }

	/**
	 * There are more bad characters than htmlspecialchars deals with.  We just want
	 * to add in some other characters to clean.  While here, we might as well
	 * have it trim out the whitespace too.
	 * @param string $string
	 * @param CONSTANT $quotes Optional, the desired constant to use for the htmlspecidalchars call
	 * @return string
	 */
	public static function escape($string,$quotes=ENT_QUOTES)
	{
		$string = htmlspecialchars(trim($string),$quotes);
		return $string;
	}

	/**
	 * Return the first $n words of the given string
	 * @param string $string Source string
	 * @param int $numWords Number of words
	 * @return string
	 */
	public static function limitWords($string,$numWords)
	{
		$output = '';
		$words = preg_split('/\s+/',$string);
		$c = 0;
		foreach($words as $word)
		{
			$output.= "$word ";
			$c++;
			if ($c >= $numWords)
			{
				$output.= '...';
				break;
			}
		}
		return $output;
	}
}
