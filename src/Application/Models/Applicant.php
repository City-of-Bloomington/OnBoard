<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

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
            if (is_array($id) || $id instanceof \ArrayObject) {
                $this->exchangeArray($id);
            }
            else {
                $db = Database::getConnection();
                $sql = 'select * from applicants where id=?';

                $result = $db->createStatement($sql)->execute([$id]);
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

    public function delete()
    {
        if ($this->getId()) {
            $db = Database::getConnection();
            $sql = 'delete from applications where applicant_id=?';
            $db->query($sql)->execute([$this->getId()]);

            foreach ($this->getFiles() as $f) { $f->delete(); }

            parent::delete();
        }
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

    public function setFirstname($s) { parent::set('firstname', $s); }
    public function setLastname ($s) { parent::set('lastname',  $s); }
    public function setEmail    ($s) { parent::set('email',     $s); }
    public function setPhone    ($s) { parent::set('phone',     $s); }
    public function setAddress  ($s) { parent::set('address',   $s); }
    public function setCity     ($s) { parent::set('city',      $s); }
    public function setZip      ($s) { parent::set('zip',       $s); }
    public function setCitylimits    ($s) { parent::set('citylimits', $s ? 1 : 0);}
    public function setOccupation    ($s) { parent::set('occupation',     $s); }

    public function handleUpdate(array $post)
    {
        $fields = [
            'firstname', 'lastname', 'email', 'phone',
            'address', 'city', 'zip',
            'citylimits', 'occupation'
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
    public function getFullname() : string
    {
        return "{$this->getFirstname()} {$this->getLastname()}";
    }

    /**
     * Applications for this applicant
     *
     * @param array $params Additional query parameters
     */
    public function getApplications(?array $params=null): array
    {
        $out = [];
        if ($this->getId()) {
            if (!$params) { $params = []; }
            $params['applicant_id'] = $this->getId();

            $table = new ApplicationTable();
            $list  = $table->find($params);
            foreach ($list as $a) { $out[] = $a; }
        }
        return $out;
    }

    /**
     * @return array An array of File objects
     */
    public function getFiles()
    {
        $files = [];
        if ($this->getId()) {
            $table = new ApplicantFilesTable();
            $list  = $table->find(['applicant_id'=>$this->getId()]);
            foreach ($list as $f) { $files[] = $f; }
        }
        return $files;
    }

    public function mergeFrom(Applicant $a)
    {
        if ($this->getId() && $a->getId()) {
            $db = Database::getConnection();

            $update = $db->createStatement('update applicantFiles set applicant_id=? where applicant_id=?');
            $update->execute([$this->getId(), $a->getId()]);

            $update = $db->createStatement('update applications   set applicant_id=? where applicant_id=?');
            $update->execute([$this->getId(), $a->getId()]);

            $delete = $db->createStatement('delete from applicants where id=?');
            $delete->execute([$a->getId()]);
        }
    }
}
