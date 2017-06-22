<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Blossom\Classes\ExternalIdentity;

class Person extends ActiveRecord
{
	protected $tablename = 'people';
	protected $race;

	public static $STATES = ['IN'];

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|string|array $id (ID, email, username)
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id) || $id instanceof ArrayObject) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from people where id=?';
				}
				elseif (false !== strpos($id,'@')) {
					$sql = 'select * from people where email=?';
				}
				else {
					$sql = 'select * from people where username=?';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('people/unknownPerson');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setAuthenticationMethod('local');
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->getFirstname() || !$this->getLastname()) {
			throw new \Exception('missingRequiredFields');
		}

		if ($this->getUsername() && !$this->getAuthenticationMethod()) {
			$this->setAuthenticationMethod('local');
		}
	}

	public function save() { parent::save(); }
	public function delete() { if ($this->isSafeToDelete()) { parent::delete(); } }

	/**
	 * Removes all the user account related fields from this Person
	 */
	public function deleteUserAccount()
	{
		$userAccountFields = array(
			'username', 'password', 'authenticationMethod', 'role'
		);
		foreach ($userAccountFields as $f) {
			$this->data[$f] = null;
		}
	}


	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()        { return parent::get('id');        }
	public function getFirstname() { return parent::get('firstname'); }
	public function getLastname()  { return parent::get('lastname');  }
	public function getAbout()     { return parent::get('about');     }
	public function getEmail()     { return parent::get('email');     }
	public function getPhone()     { return parent::get('phone');     }
	public function getAddress()   { return parent::get('address');   }
	public function getCity()      { return parent::get('city');      }
	public function getState()     { return parent::get('state');     }
	public function getZip()       { return parent::get('zip');       }
	public function getWebsite()   { return parent::get('website');   }
	public function getGender()    { return parent::get('gender');    }
	public function getRace_id()   { return parent::get('race_id');   }
	public function getRace()      { return parent::getForeignKeyObject(__namespace__.'\Race', 'race_id'); }

	public function setFirstname($s) { parent::set('firstname', $s); }
	public function setLastname ($s) { parent::set('lastname',  $s); }
	public function setAbout    ($s) { parent::set('about',     $s); }
	public function setEmail    ($s) { parent::set('email',     $s); }
	public function setPhone    ($s) { parent::set('phone',     $s); }
	public function setAddress  ($s) { parent::set('address',   $s); }
	public function setCity     ($s) { parent::set('city',      $s); }
	public function setState    ($s) { parent::set('state',     $s); }
	public function setZip      ($s) { parent::set('zip',       $s); }
	public function setWebsite  ($s) { parent::set('website',   $s); }
	public function setRace_id  ($i) { parent::setForeignKeyField (__namespace__.'\Race', 'race_id', $i); }
	public function setRace     ($o) { parent::setForeignKeyObject(__namespace__.'\Race', 'race_id', $o); }
	public function setGender   ($s)
	{
		strtolower(trim($s)) == 'male'
			? parent::set('gender', 'male')
			: parent::set('gender', 'female');
	}

	public function getUsername()             { return parent::get('username'); }
	public function getPassword()             { return parent::get('password'); } # Encrypted
	public function getRole()                 { return parent::get('role');     }
	public function getAuthenticationMethod() { return parent::get('authenticationMethod'); }

	public function setUsername            ($s) { parent::set('username',             $s); }
	public function setRole                ($s) { parent::set('role',                 $s); }
	public function setAuthenticationMethod($s) { parent::set('authenticationMethod', $s); }

	public function setPassword($s)
	{
		$s = trim($s);
		if ($s) { $this->data['password'] = sha1($s); }
		else    { $this->data['password'] = null;     }
	}

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$fields = [
            'firstname', 'middlename', 'lastname', 'about', 'gender', 'race_id',
            'email', 'phone', 'address', 'city', 'state', 'zip', 'website'
        ];
		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}
	}

	/**
	 * @param array $post
	 */
	public function handleUpdateUserAccount($post)
	{
        global $DIRECTORY_CONFIG;

		$fields = ['username', 'email', 'authenticationMethod', 'role'];
		foreach ($fields as $f) {
			if (isset($post[$f])) {
				$set = 'set'.ucfirst($f);
				$this->$set($post[$f]);
			}
		}

        if (!empty($post['password'])) {
            $this->setPassword($post['password']);
        }

		$method = $this->getAuthenticationMethod();
		if ($this->getUsername() && $method && $method != 'local') {
            $class = $DIRECTORY_CONFIG[$method]['classname'];
			$identity = new $class($this->getUsername());
			$this->populateFromExternalIdentity($identity);
		}
	}

	//----------------------------------------------------------------
	// User Authentication
	//----------------------------------------------------------------
	/**
	 * Should provide the list of methods supported
	 *
	 * There should always be at least one method, called "local"
	 * Additional methods must match classes that implement External Identities
	 * See: ExternalIdentity.php
	 *
	 * @return array
	 */
	public static function getAuthenticationMethods()
	{
		global $DIRECTORY_CONFIG;
		return array_merge(['local'], array_keys($DIRECTORY_CONFIG));
	}

	/**
	 * Determines which authentication scheme to use for the user and calls the appropriate method
	 *
	 * Local users will get authenticated against the database
	 * Other authenticationMethods will need to write a class implementing ExternalIdentity
	 * See: /libraries/framework/classes/ExternalIdentity.php
	 *
	 * @param string $password
	 * @return boolean
	 */
	public function authenticate($password)
	{
        global $DIRECTORY_CONFIG;

		if ($this->getUsername()) {
			switch($this->getAuthenticationMethod()) {
				case "local":
					return $this->getPassword()==sha1($password);
				break;

				default:
					$method = $this->getAuthenticationMethod();
					$class = $DIRECTORY_CONFIG[$method]['classname'];
					return $class::authenticate($this->getUsername(),$password);
			}
		}
	}

	/**
	 * Checks if the user is supposed to have acces to the resource
	 *
	 * This is implemented by checking against a Zend_Acl object
	 * The Zend_Acl should be created in bootstrap.inc
	 *
	 * @param string $resource
	 * @param string $action
	 * @return boolean
	 */
	public static function isAllowed($resource, $action=null)
	{
		global $ZEND_ACL;

		$role = 'Anonymous';
		if (isset(  $_SESSION['USER']) && $_SESSION['USER']->getRole()) {
			$role = $_SESSION['USER']->getRole();
		}
		return $ZEND_ACL->isAllowed($role, $resource, $action);
	}

	/**
	 * @param ExternalIdentity $identity An object implementing ExternalIdentity
	 */
	public function populateFromExternalIdentity(ExternalIdentity $identity)
	{
		if (!$this->getFirstname() && $identity->getFirstname()) {
			$this->setFirstname($identity->getFirstname());
		}
		if (!$this->getLastname() && $identity->getLastname()) {
			$this->setLastname($identity->getLastname());
		}
		if (!$this->getEmail() && $identity->getEmail()) {
			$this->setEmail($identity->getEmail());
		}
	}
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFullname()
	{
		return "{$this->getFirstname()} {$this->getLastname()}";
	}

	/**
	 * @return string
	 */
	public function getUrl() { return BASE_URL.'/people/view?person_id='.$this->getId(); }
	public function getUri() { return BASE_URI.'/people/view?person_id='.$this->getId(); }

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getMemberCommittees()
	{
		$table = new CommitteeTable();
		return $table->find(['member_id'=>$this->getId()]);
	}

	/**
	 * @return array An array of Committee objects
	 */
	public function getLiaisonCommittees()
	{
        $sql = 'select distinct c.*
                from liaisons l
                join committees c on l.committee_id=c.id
                where l.person_id=?';
        $zend_db = Database::getConnection();
        $result = $zend_db->query($sql, [$this->getId()]);

        $committees = [];
        foreach ($result->toArray() as $row) {
            $committees[] = new Committee($row);
        }
        return $committees;
	}

	/**
	 * @param array $fields Extra fields to search on
	 * @return Zend\Db\ResultSet
	 */
	public function getMembers($fields=null)
	{
        $fields['person_id'] = $this->getId();

		$table = new MemberTable();
		return $table->find($fields);
	}

	/**
	 * @return array An array of People in the same committes
	 */
	public function getPeers()
	{
		$peers = array();

		$committees = array();
		foreach ($this->getMemberCommittees() as $committee) {
			$committees[] = $committee->getId();
		}
		if (count($committees)) {
			$table = new PeopleTable();
			$list = $table->find(['committee_id'=>$committees]);
			foreach ($list as $person) {
				if ($person->getId() != $this->getId()) {
					$peers[] = $person;
				}
			}
		}
		return $peers;
	}

	/**
	 * Returns the offices held for the given committee
	 *
	 * If a date is given, it will return only offices held on that date
	 *
	 * @param Committee $committee
	 * @param string $date
	 * @return array An array of Office objects
	 */
	public function getOffices(Committee $committee=null, $date=null)
	{
		$search = ['person_id'=>$this->getId()];
		if ($committee) {
			$search['committee_id'] = $committee->getId();
		}
		if ($date) {
			$search['current'] = $date;
		}


		$offices = [];
		$table = new OfficeTable();
		foreach ($table->find($search) as $o) {
            $offices[] = $o;
		}
		return $offices;
	}

	/**
	 * Returns all the appointment information for a person.
	 *
	 * Optionally provide a committee to limit the appointment information
	 *
	 * @param Committee $committee
	 * @return Zend\Db\ResultSet
	 */
	public function getAppointers(Committee $committee=null)
	{
		$search = ['person_id'=>$this->getId()];
		if ($committee) {
			$search['committee_id'] = $committee->getId();
		}
		$table = new AppointerTable();
		return $table->find($search);
	}

	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
        $id = (int)$this->getId();

        $sql = "select id from members  where person_id=$id
          union select id from liaisons where person_id=$id
          union select id from offices  where person_id=$id";
        $zend_db = Database::getConnection();
        $result  = $zend_db->query($sql)->execute();
        return count($result) ? false : true;
	}
}
