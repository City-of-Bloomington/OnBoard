<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Web\ActiveRecord;
use Web\Database;

class ActionType extends ActiveRecord
{
	protected $tablename = 'legislationActionTypes';

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
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from legislationActionTypes where id=?';
				}
				else {
					$sql = 'select * from legislationActionTypes where name=?';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('legislationActionTypes/unknown');
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
	public function getId()       { return parent::get('id'      ); }
	public function getName()     { return parent::get('name'    ); }
	public function getOrdering() { return parent::get('ordering'); }

	public function setName    ($s) { parent::set('name',     $s); }
	public function setOrdering($s) { parent::set('ordering', $s); }

	public function handleUpdate(array $post)
	{
        $this->setName    ($post['name'    ]);
        $this->setOrdering($post['ordering']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }
}
