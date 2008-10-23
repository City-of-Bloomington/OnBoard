<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class ActiveRecord
{
	abstract public function save();

	#----------------------------------------------------------------------------
	# Forms will be doing dates and times as drop downs.
	# They should come in as arrays with indexes matching those used by PHP's getdate()
	#
	# These functions handle converting back and forth between MySQL's date
	# format and PHP's getdate() array
	#----------------------------------------------------------------------------
	public function dateArrayToString(array $date)
	{
		if ($date['year'] && $date['mon'] && $date['mday'])
		{
			$dateString = "$date[year]-$date[mon]-$date[mday]";

			if (isset($date['hours']) || isset($date['minutes']) || isset($date['seconds']))
			{
				$time = (isset($date['hours']) && $date['hours']) ? "$date[hours]:" : '00:';
				$time.= (isset($date['minutes']) && $date['minutes']) ? "$date[minutes]:" : '00:';
				$time.= (isset($date['seconds']) && $date['seconds']) ? $date['seconds'] : '00';

				$dateString.= " $time";
			}
			return $dateString;
		}
		else return null;
	}

	public function dateStringToArray($string)
	{
		if ($string)
		{
			$datetime = explode(' ',$string);
			$date = explode("-",$datetime[0]);
			$getdate['year'] = $date[0];
			$getdate['mon'] = $date[1];
			$getdate['mday'] = $date[2];

			if (isset($datetime[1]) && preg_match('/[\d]*:[\d]{2}:[\d]{2}/',$datetime[1]))
			{
				$time = explode(':',$datetime[1]);
				if ($time[0]!=0 || $time[1]!=0 || $time[2]!=0)
				{
					$getdate['hours'] = $time[0];
					$getdate['minutes'] = $time[1];
					$getdate['seconds'] = $time[2];
				}
			}
			return $getdate;
		}
		else return null;
	}

	public function dateArrayToTimestamp(array $date)
	{
		$hours = isset($date['hours']) ? $date['hours'] : 0;
		$minutes = isset($date['minutes']) ? $date['minutes'] : 0;
		$seconds = isset($date['seconds']) ? $date['seconds'] : 0;

		if ($date['mon'] && $date['mday'] && $date['year'])
		{
			return mktime($hours,$minutes,$seconds,$date['mon'],$date['mday'],$date['year']);
		}
	}
}
