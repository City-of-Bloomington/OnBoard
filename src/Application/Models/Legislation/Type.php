<?php
/**
 * @copyright 2017-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Legislation;

use Web\ActiveRecord;
use Web\Database;

class Type extends ActiveRecord
{
	protected $tablename = 'legislationTypes';

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
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from legislationTypes where id=?';
				}
				else {
					$sql = 'select * from legislationTypes where name=?';
				}
				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('legislationTypes/unknown');
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
		if (!$this->getName()) { throw new \Exception('missingName'); }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()      { return parent::get('id'  ); }
	public function getName()    { return parent::get('name'); }
	public function getSubtype() { return parent::get('subtype') ? true : false; }

	public function setName   ($s) { parent::set('name',    $s); }
	public function setSubtype($b) { $this->data['subtype'] = $b ? 1 : 0; }

	public function handleUpdate(array $post)
	{
        $this->setName   ($post['name'   ]);
        $this->setSubtype($post['subtype']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }
	public function toArray(): array { return $this->data; }

	public function isSubtype() { return $this->getSubtype() ? true : false; }
}
