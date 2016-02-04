<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Application extends ActiveRecord
{
    protected $tablename = 'applications';
	protected $committees = [];

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
                $sql = 'select * from applications where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new Exception('applications/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
        if (!$this->getFirstname() || !$this->getLastname() || !$this->getEmail()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save()
	{
        // Let MySQL handle the timestamp
        if (isset($this->data['created' ])) { unset($this->data['created' ]); }
        parent::save();
	}
	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()        { return parent::get('id');        }
	public function getFirstname() { return parent::get('firstname'); }
	public function getLastname()  { return parent::get('lastname');  }
	public function getEmail()     { return parent::get('email');     }
	public function getPhone()     { return parent::get('phone');     }
	public function getCreated ($f=null) { return parent::getDateData('created',  $f); }

	public function setFirstname($s) { parent::set('firstname', $s); }
	public function setLastname ($s) { parent::set('lastname',  $s); }
	public function setEmail    ($s) { parent::set('email',     $s); }
	public function setPhone    ($s) { parent::set('phone',     $s); }

	public function handleUpdate(array $post)
	{
        $fields = ['firstname', 'lastname', 'email', 'phone'];

		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}

		if (isset($post['committees'])) {
            $this->setCommittees(array_keys($post['committees']));
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Sets all the committees for this application
	 *
	 * You should only set the full list of committees once, during
	 * the inital application form submission.
	 * After that, we'll need to use a different api for adjusting the
	 * committees associated with this application.
	 *
	 * @param array $ids An array of committee ID
	 */
	public function setCommittees(array $ids)
	{
        if (!$this->getId() && $ids) {
            foreach ($ids as $id) {
                try { $this->committees[$id] = new Committee($id); }
                catch (\Exception $e) {
                    // Just ignore invalid committees for now
                }
            }
        }
 	}
}