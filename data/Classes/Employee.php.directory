<?php
/**
 * A class for working with a Directory webservice
 *
 * This class is written specifically for the City of Bloomington's
 * Directory webservice.  If you are going to be doing authentication
 * with your own webservice, you will probably need to customize
 * the this class.
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Site\Classes;

use Blossom\Classes\ExternalIdentity;
use Blossom\Classes\Url;

class Employee implements ExternalIdentity
{
	private static $connection;
	private $config;
	private $entry;

	/**
	 * @param  array     $config
	 * @param  string    $username
	 * @param  string    $password
	 * @throws Exception
	 */
	public static function authenticate($username, $password)
	{
        return false;
	}


	/**
	 * Loads an entry from the webservice for the given user
	 *
	 * @param array  $config
	 * @param string $username
	 */
	public function __construct($username)
	{
		global $DIRECTORY_CONFIG;
		$this->config = $DIRECTORY_CONFIG['Employee'];

		$url = $this->config['DIRECTORY_SERVER'].'/people/view?format=json;username='.$username;
		$response = Url::get($url);
		if ($response) {
            $json = json_decode($response);
            if (!$json)                { throw new \Exception('employee/invalidResponse'); }
            if (!empty($json->errors)) { throw new \Exception('ldap/unknownUser'        ); }
            $this->entry = $json;
		}
		else {
            throw new \Exception('ldap/unknownUser');
		}
	}

	public function get($field)
	{
        if (isset($this->entry->$field)) { return $this->entry->$field; }
	}

	/**
	 * @return string
	 */
	public function getUsername()	{ return $this->get('username' ); }
	public function getFirstname()	{ return $this->get('firstname'); }
	public function getLastname()	{ return $this->get('lastname' ); }
	public function getEmail()		{ return $this->get('email'    ); }
	public function getPhone()		{ return $this->get('office'   ); }
	public function getAddress()	{ return $this->get('address'  ); }
	public function getCity()		{ return $this->get('city'     ); }
	public function getState()		{ return $this->get('state'    ); }
	public function getZip()		{ return $this->get('zip'      ); }
}
