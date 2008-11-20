<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class SingleSignOn
{
	/**
	 * Checks with the single sign on web-service to see
	 * if there is a ticket that matches the user's cookie
	 * @param string $cookie_name
	 * @param url $service
	 */
	public static function verifyUser($cookie_name,$service,$ip_address)
	{
		if (isset($_COOKIE[$cookie_name]))
		{
			$ticket = file_get_contents("$service/tickets/verifyTicket.php?format=txt;id=$_COOKIE[$cookie_name];ip_address=$ip_address");
			if ($ticket)
			{
				$ticket = explode('|',$ticket);
				if ($ticket[0] != 'error')
				{
					try
					{
						$user = new User($ticket[0]);
						return $user;
					}
					catch (Exception $e) { }
				}
			}
		}
	}
}
