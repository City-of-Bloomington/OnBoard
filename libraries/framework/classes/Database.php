<?php
/**
 * Singleton for the Database connection
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Database
{
	private static $pdo;

	/**
	 * @param boolean $reconnect If true, drops the connection and reconnects
	 * @return resource
	 */
	public static function getConnection($reconnect=false)
	{
		if ($reconnect) {
			self::$pdo=null;
		}
		if (!self::$pdo) {
			try {
				self::$pdo = new PDO(DB_TYPE.':'.DB_DSN.'dbname='.DB_NAME,
										DB_USER,
										DB_PASS,
										array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
			}
			catch (PDOException $e) {
				die($e->getMessage());
			}
		}
		return self::$pdo;
	}
}
