<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Tag extends ActiveRecord
{
	protected $tablename = 'tags';

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
					$sql = 'select * from tags where id=?';
				}
				else {
					$sql = 'select * from tags where name=?';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('tags/unknownTag');
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

	public function delete()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->query('delete from topic_tags where tag_id=?', [$this->getId()]);

			parent::delete();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()   { return parent::get('id');   }
	public function getName() { return parent::get('name'); }

	public function setName($s) { parent::set('name', $s); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	/**
	 * @return string
	 */
	public function getUrl() { return BASE_URL.'/tags/view?tag_id='.$this->getId(); }
	public function getUri() { return BASE_URI.'/tags/view?tag_id='.$this->getId(); }

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getTopics()
	{
		$table = new TopicTable();
		return $table->find(['tag_id'=>$this->getId()]);
	}

	/**
	 * Returns the count of how many Topics use this tag
	 *
	 * @return int
	 */
	public function getPopularity()
	{
		if ($this->getId()) {
			$sql = "select count(*) as popularity from topic_tags where tag_id=? group by tag_id";
			$zend_db = Database::getConnection();
			$result = $zend_db->createStatement($sql)->execute([$this->getId()]);
			return $result->current()['popularity'];
		}
	}
}
