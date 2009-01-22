<?php
/**
 * Helper functions to convert between database types and PHP types
 *
 * These functions handle converting back and forth between MySQL's date
 * format and PHP's getdate() array
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class ActiveRecord
{
	abstract public function save();

	/**
	 * Converts from a PHP getdate array into a MySQL datetime string
	 *
	 * If there is no time information, then the MySQL string will only be a date
	 *
	 * @param array $date
	 * @return string
	 */
	public function dateArrayToString(array $date)
	{
		if ($date['year'] && $date['mon'] && $date['mday']) {
			$dateString = "$date[year]-$date[mon]-$date[mday]";

			if (isset($date['hours']) || isset($date['minutes']) || isset($date['seconds'])) {
				$time = (isset($date['hours']) && $date['hours']) ? "$date[hours]:" : '00:';
				$time.= (isset($date['minutes']) && $date['minutes']) ? "$date[minutes]:" : '00:';
				$time.= (isset($date['seconds']) && $date['seconds']) ? $date['seconds'] : '00';

				$dateString.= " $time";
			}
			return $dateString;
		}
		return null;
	}

	/**
	 * Converts from a MySQL datetime string into a PHP getdate array
	 *
	 * @param string $string
	 */
	public function dateStringToArray($string)
	{
		if ($string) {
			$datetime = explode(' ',$string);
			$date = explode("-",$datetime[0]);
			$getdate['year'] = $date[0];
			$getdate['mon'] = $date[1];
			$getdate['mday'] = $date[2];

			if (isset($datetime[1]) && preg_match('/[\d]*:[\d]{2}:[\d]{2}/',$datetime[1])) {
				$time = explode(':',$datetime[1]);
				if ($time[0]!=0 || $time[1]!=0 || $time[2]!=0) {
					$getdate['hours'] = $time[0];
					$getdate['minutes'] = $time[1];
					$getdate['seconds'] = $time[2];
				}
			}
			return $getdate;
		}
		return null;
	}

	/**
	 * Converts from a PHP getdate array into a timestamp
	 *
	 * @param array $date
	 * @return int
	 */
	public function dateArrayToTimestamp(array $date)
	{
		$hours = isset($date['hours']) ? $date['hours'] : 0;
		$minutes = isset($date['minutes']) ? $date['minutes'] : 0;
		$seconds = isset($date['seconds']) ? $date['seconds'] : 0;

		if ($date['mon'] && $date['mday'] && $date['year']) {
			return mktime($hours,$minutes,$seconds,$date['mon'],$date['mday'],$date['year']);
		}
	}
}
