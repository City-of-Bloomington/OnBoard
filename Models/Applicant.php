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

    public static $referralOptions = [
        'Herald-Times', 'Radio', 'City Council Meeting', 'City Staff', 'City Website',
        'Community Organization', 'Facebook', 'Press Release', 'Other'
    ];

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
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('applicants/unknown');
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
        // Generate a timestamp for created
        if (!$this->getCreated()) {
            $this->data['created'] = date(ActiveRecord::MYSQL_DATETIME_FORMAT);
        }
        else {
            // This timestamp should not be updated after the initial insert
            unset($this->data['created']);
        }

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
	public function getAddress()   { return parent::get('address');   }
	public function getCity()      { return parent::get('city');      }
	public function getZip()       { return parent::get('zip');       }
	public function getCreated ($f=null) { return parent::getDateData('created',  $f); }
    public function getCitylimits    () { return parent::get('citylimits'    ); }
    public function getOccupation    () { return parent::get('occupation'    ); }
    public function getReferredFrom  () { return parent::get('referredFrom'  ); }
    public function getReferredOther () { return parent::get('referredOther' ); }
    public function getInterest      () { return parent::get('interest'      ); }
    public function getQualifications() { return parent::get('qualifications'); }

	public function setFirstname($s) { parent::set('firstname', $s); }
	public function setLastname ($s) { parent::set('lastname',  $s); }
	public function setEmail    ($s) { parent::set('email',     $s); }
	public function setPhone    ($s) { parent::set('phone',     $s); }
	public function setAddress  ($s) { parent::set('address',   $s); }
	public function setCity     ($s) { parent::set('city',      $s); }
	public function setZip      ($s) { parent::set('zip',       $s); }
	public function setCitylimits    ($s) { parent::set('citylimits', $s ? 1 : 0);}
	public function setOccupation    ($s) { parent::set('occupation',     $s); }
	public function setReferredFrom  ($s) { parent::set('referredFrom',   $s); }
	public function setReferredOther ($s) { parent::set('referredOther',  $s); }
	public function setInterest      ($s) { parent::set('interest',       $s); }
	public function setQualifications($s) { parent::set('qualifications', $s); }

	public function handleUpdate(array $post)
	{
        $fields = [
            'firstname', 'lastname', 'email', 'phone',
            'address', 'city', 'zip',
            'citylimits', 'occupation', 'interest', 'qualifications',
            'referredFrom', 'referredOther'
        ];

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
	 * @return array An array of Media objects
	 */
	public function getMedia()
	{
        $media = [];
        if ($this->getId()) {
            $table = new MediaTable();
            $list  = $table->find(['applicant_id'=>$this->getId()]);
            foreach ($list as $m) { $media[] = $m; }
        }
        return $media;
	}

}