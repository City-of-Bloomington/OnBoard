<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class Liaison extends ActiveRecord
{
    const TYPE_DEPARTMENTAL = 'departmental';
    const TYPE_LEGAL        = 'legal';

	protected $tablename = 'liaisons';

	protected $committee;
	protected $person;

	public static $types = ['departmental', 'legal'];

	/**
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$db = Database::getConnection();
				$sql = 'select * from liaisons where id=?';

				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('liaisons/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
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
    }

    public function save()   { parent::save(); }
    public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getType()         { return parent::get('type'        ); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getPerson_id()    { return parent::get('person_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'); }

	public function setType($s) { if (in_array($s, self::$types)) { parent::set('type', $s); } }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }

	public function handleUpdate(array $post)
	{
        $this->setType($post['type']);
        $this->setCommittee_id($post['committee_id']);
        $this->setPerson_id($post['person_id']);
	}
}
