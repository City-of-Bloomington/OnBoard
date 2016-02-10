<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Applicant extends ActiveRecord
{
    protected $tablename = 'applicants';

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
                $sql = 'select * from applicants where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new Exception('applicants/unknown');
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
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Applications for this applicant
	 *
	 * @param array $params Additional query parameters
	 * @return Zend\Db\Result
	 */
	public function getApplications(array $params=null)
	{
        if ($this->getId()) {
            if (!$params) { $params = []; }
            $params['applicant_id'] = $this->getId();

            $table = new ApplicationTable();
            return $table->find($params);
        }
	}

	public function saveCommittees(array $ids)
	{
        $currentApplications = $this->getApplications();
        $currentCommittees = [];
        foreach ($currentApplications as $a) {
            $currentCommittees[] = $a->getCommittee_id();
        }

        $zend_db = Database::getConnection();

        $delete = $zend_db->createStatement('delete from applications where committee_id=? and applicant_id=?');
        $toDelete = array_diff($currentCommittees, $ids);
        foreach ($toDelete as $committee_id) {
            $delete->execute([$committee_id, $this->getId()]);
        }

        $insert = $zend_db->createStatement('insert applications set committee_id=?,applicant_id=?');
        $toInsert = array_diff($ids, $currentCommittees);
        foreach ($toInsert as $committee_id) {
            $insert->execute([$committee_id, $this->getId()]);
        }
	}

	/**
	 * @return Zend\Db\Result
	 */
	public function getMedia()
	{
        if ($this->getId()) {
            $table = new MediaTable();
            return $table->find(['applicant_id'=>$this->getId()]);
        }
	}

}