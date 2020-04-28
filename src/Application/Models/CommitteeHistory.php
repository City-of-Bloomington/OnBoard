<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class CommitteeHistory extends ActiveRecord
{
    const STATE_ORIGINAL = 'original';
    const STATE_UPDATED  = 'updated';
    public static $states = [self::STATE_ORIGINAL, self::STATE_UPDATED];

	protected $tablename = 'committeeHistory';

	protected $committee;
	protected $person;

	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select * from {$this->tablename} where id=?";

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception("{$this->tablename}/unknown");
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['date'] = date(ActiveRecord::MYSQL_DATETIME_FORMAT);
			$this->setPerson($_SESSION['USER']);
		}
	}

	public function validate()
	{
        $requiredFields = ['tablename', 'action', 'committee_id', 'person_id'];
        foreach ($requiredFields as $f) {
            $field = ucfirst($f);
            $get   = 'get'.$field;
            if (!$this->$get()) {
                throw new \Exception("{$this->tablename}/missing$field");
            }
        }
	}

	public function save()
	{
        // Let MySQL generate the actual timestamp
        unset($this->data['date']);

        parent::save();
    }

    public static function saveNewEntry(array $entry)
    {
        $h = new CommitteeHistory();
        $h->setCommittee_id($entry['committee_id']);
        $h->setTablename   ($entry['tablename'   ]);
        $h->setAction      ($entry['action'      ]);
        $h->setChanges     ($entry['changes'     ]);
        $h->save();
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getTablename()    { return parent::get('tablename'   ); }
	public function getAction()       { return parent::get('action'      ); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getPerson_id()    { return parent::get('person_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'   ); }
	public function getDate($f=null, $tz=null) { return parent::getDateData('date', $f, $tz); }
	public function getChanges() { return json_decode(parent::get('changes'), true); }

	public function setTablename   ($s) { parent::set('tablename', $s); }
	public function setAction      ($s) { parent::set('action',    $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }
	public function setChanges(array $d=null) { parent::set('changes', json_encode($d)); }
}
