<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class Office extends ActiveRecord
{
    protected $tablename = 'offices';

    protected $committee;
    protected $person;

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
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $db = Database::getConnection();
                $sql = 'select * from offices where id=?';

                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('offices/unknownOffice');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
            $this->setStartDate(date('Y-m-d'));
        }
    }

    /**
     * Throws an exception if anything's wrong
     * @throws Exception $e
     */
    public function validate()
    {
        if (!$this->getCommittee_id() || !$this->getPerson_id()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!$this->getTitle()) {
            throw new \Exception('offices/missingTitle');
        }

        // Make sure the end date falls after the start date
        $start = $this->getStartDate('U');
        $end   = $this->getEndDate  ('U');
        if ($end && $end < $start) {
            throw new \Exception('invalidEndDateBeforeStart');
        }
    }

    public function save() { parent::save(); }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()           { return parent::get('id');   }
    public function getTitle()        { return parent::get('title'); }
    public function getCommittee_id() { return parent::get('committee_id'); }
    public function getPerson_id()    { return parent::get('person_id'); }
    public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
    public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'); }
    public function getStartDate(?string $format=null)  { return parent::getDateData('startDate', $format); }
    public function getEndDate  (?string $format=null)  { return parent::getDateData('endDate',   $format); }

    public function setTitle       ($s) { parent::set('title', $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
    public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
    public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }
    public function setStartDate(?string $date=null, ?string $format='Y-m-d') { parent::setDateData('startDate', $date, $format); }
    public function setEndDate  (?string $date=null, ?string $format='Y-m-d') { parent::setDateData('endDate',   $date, $format); }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function __toString() { return $this->getTitle(); }
}
