<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Web\ActiveRecord;
use Web\Database;

class Action extends ActiveRecord
{
	protected $tablename = 'legislationActions';
	protected $legislation;
	protected $type;

	public static $outcomes = ['pass', 'fail', 'withdrawn'];

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
				$zend_db = Database::getConnection();
                $sql = 'select * from legislationActions where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('legislationActions/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setActionDate(date(DATE_FORMAT));
		}
	}

	public function validate()
	{
        if (!$this->getLegislation_id() || !$this->getType_id() || !$this->getActionDate()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()             { return parent::get('id'            ); }
	public function getLegislation_id() { return parent::get('legislation_id'); }
	public function getType_id()        { return parent::get('type_id'       ); }
	public function getOutcome()        { return parent::get('outcome'       ); }
	public function getVote()           { return parent::get('vote'          ); }
	public function getActionDate($format=null) { return parent::getDateData('actionDate', $format); }
	public function getLegislation()    { return parent::getForeignKeyObject(__namespace__.'\Legislation', 'legislation_id'); }
	public function getType()           { return parent::getForeignKeyObject(__namespace__.'\ActionType',  'type_id'       ); }

	public function setLegislation_id         ($i) { parent::setForeignKeyField (__namespace__.'\Legislation', 'legislation_id', $i); }
	public function setLegislation(Legislation $o) { parent::setForeignKeyObject(__namespace__.'\Legislation', 'legislation_id', $o); }
	public function setType_id                ($i) { parent::setForeignKeyField (__namespace__.'\ActionType', 'type_id', $i); }
	public function setType        (ActionType $o) { parent::setForeignKeyObject(__namespace__.'\ActionType', 'type_id', $o); }
	public function setActionDate($d) { parent::setDateData('actionDate', $d); }
	public function setOutcome   ($s) { parent::set('outcome', $s); }
	public function setVote      ($s) { parent::set('vote',    $s); }

	public function handleUpdate(array $post)
	{
        $fields = ['legislation_id', 'type_id', 'actionDate', 'outcome', 'vote'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}
}
