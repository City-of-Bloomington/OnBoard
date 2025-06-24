<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class Application extends ActiveRecord
{
    protected $tablename = 'applications';
    protected $applicant;
    protected $committee;

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
                $db = Database::getConnection();
                $sql = 'select * from applications where id=?';

                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('applications/unknown');
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
        if (!$this->getCommittee_id() || !$this->getApplicant_id()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save()
    {
        // Let MySQL handle setting the timestamp
        if (isset($this->data['created' ])) { unset($this->data['created' ]); }

        parent::save();
    }

    public function delete() { parent::delete(); }

    public function archive()
    {
        if ($this->getId()) {
            $sql = 'update applications set archived=now() where id=?';
            $db = Database::getConnection();
            $db->query($sql, [$this->getId()]);
        }
    }

    public function unarchive()
    {
        if ($this->getId()) {
            $sql = 'update applications set archived=null where id=?';
            $db = Database::getConnection();
            $db->query($sql, [$this->getId()]);
        }
    }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()           { return parent::get('id');   }
    public function getCommittee_id() { return parent::get('committee_id'); }
    public function getApplicant_id() { return parent::get('applicant_id'); }
    public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
    public function getApplicant()    { return parent::getForeignKeyObject(__namespace__.'\Applicant', 'applicant_id'); }
    public function getCreated ($f=null) { return parent::getDateData('created',  $f); }
    public function getArchived($f=null) { return parent::getDateData('archived', $f); }

    public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
    public function setApplicant_id($i) { parent::setForeignKeyField (__namespace__.'\Applicant', 'applicant_id', $i); }
    public function setCommittee($o)    { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
    public function setApplicant($o)    { parent::setForeignKeyObject(__namespace__.'\Applicant', 'applicant_id', $o); }
    public function setArchived ($d)    { parent::setDateData('archived', $d); }

    //----------------------------------------------------------------
    // Custom functions
    //----------------------------------------------------------------
    /**
     * @param string $format Date format
     * @return string
     */
    public function getExpires($format=null)
    {
        if (!$format) { $format = DATE_FORMAT; }

        $d = $this->getCommittee()->getApplicationLifetime();

        $expires = new \DateTime($this->getCreated());
        $expires->add(new \DateInterval("P{$d}D"));
        return $expires->format($format);
    }

    public function getPeopleToNotify() : array
    {
        $people = [];

        $sql = "select p.*
                from people       p
                join liaisons     l on p.id=l.person_id
                join applications a on l.committee_id=a.committee_id
                where a.id=?
                  and type=?";
        $db = Database::getConnection();
        $result  = $db->createStatement($sql)->execute([$this->getId(), Liaison::TYPE_DEPARTMENTAL]);
        foreach ($result as $p) { $people[] = new Person($p); }

        return $people;
    }
}
