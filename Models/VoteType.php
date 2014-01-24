<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class VoteType extends ActiveRecord
{
	protected $tablename = 'voteTypes';

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
				$sql = 'select * from voteTypes where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('voteTypes/unknownVoteType');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$max = self::getMaxOrdering();
			$this->setOrdering($max++);
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getName()) { throw new \Exception('missingName'); }

		if (!$this->ordering) {
			$max = self::getMaxOrdering();
			$this->setOrdering($max++);
		}
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()       { return parent::get('id');   }
	public function getName()     { return parent::get('name'); }
	public function getOrdering() { return parent::get('ordering'); }

	public function setName    ($s) { parent::set('name', $s); }
	public function setOrdering($i) { parent::set('ordering', (int)$i); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	/**
	 * @return int
	 */
	public static function getMaxOrdering()
	{
		$zend_db = Database::getConnection();
		$result = $zend_db->createStatement('select max(ordering) as max from voteTypes')->execute();
		return $result->current()['max'];
	}

	/**
	 * Returns the last vote type in the sequence, as determined by the ordering
	 * @return VoteType
	 */
	public static function getFinalVoteType()
	{
		$zend_db = Database::getConnection();
		$result = $zend_db->createStatement('select id from voteTypes order by ordering desc limit 1')->execute();
		return new VoteType($result->current()['id']);
	}
}
