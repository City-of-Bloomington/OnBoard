<?php
/**
 * Manages singletons for database connections
 *
 * Allows for connecting to multiple databases, using
 * only a single instance for each database connection.
 *
 * @copyright 2006-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;
use Laminas\Db\Adapter\Adapter;

class Database
{
	private static $connections = [];

	/**
	 * @param boolean $reconnect If true, drops the connection and reconnects
	 * @param string $db         Label for database configuration
	 * @return resource
	 */
	public static function getConnection($reconnect=false, $db='default')
	{
        global $DATABASES;

		if ($reconnect) {
            if (isset(self::$connections[$db])) { unset(self::$connections[$db]); }
		}
        if (!isset(self::$connections[$db])) {
			try {
				self::$connections[$db] = new Adapter($DATABASES[$db]);
			}
			catch (Exception $e) { die($e->getMessage()); }
		}
		return self::$connections[$db];
	}
}
