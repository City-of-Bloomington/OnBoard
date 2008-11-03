<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Database
{
	private static $PDO;

	public static function getConnection($reconnect=false)
	{
		if ($reconnect) { self::$PDO=null; }
		if(!self::$PDO)
		{
			try
			{
				self::$PDO = new PDO(DB_TYPE.':'.DB_DSN.'dbname='.DB_NAME,
										DB_USER,
										DB_PASS,
										array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
			}
			catch (PDOException $e) { die($e->getMessage()); }
		}
		return self::$PDO;
	}
}
