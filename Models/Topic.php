<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Topic extends ActiveRecord
{
	protected $tablename = 'topics';

	protected $topicType;
	protected $committee;

	private $tags = [];

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
				$sql = 'select * from topics where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('topics/unknownTopic');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDate(date(DATE_FORMAT));
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->getTopicType_id() || !$this->getNumber() || !$this->getCommittee_id()) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getDate()) { $this->setDate(date(DATE_FORMAT)); }
	}

	public function save()
	{
		parent::save();
		$this->saveTags();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id');   }
	public function getNumber()       { return parent::get('number'); }
	public function getDescription()  { return parent::get('description'); }
	public function getSynopsis()     { return parent::get('synopsis'); }
	public function getTopicType_id() { return parent::get('topicType_id'); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getTopicType()    { return parent::getForeignKeyObject(__namespace__.'\TopicType', 'topicType_id'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getDate($f=null)  { return parent::getDateData('date', $f); }

	public function setNumber      ($s) { parent::set('number',      $s); }
	public function setDescription ($s) { parent::set('description', $s); }
	public function setSynopsis    ($s) { parent::set('synopsis',    $s); }
	public function setTopicType_id($i) { parent::setForeignKeyField (__namespace__.'\TopicType', 'topicType_id', $i); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setTopicType   ($o) { parent::setForeignKeyObject(__namespace__.'\TopicType', 'topicType_id', $o); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setDate        ($d) { parent::setDateData('date', $d); }

	public function handleUpdate($post)
	{
		$fields = ['topicType_id', 'number', 'description', 'synopsis', 'date', 'tags'];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getUrl() { return BASE_URL.'/topics/view?topic_id='.$this->getId(); }
	public function getUri() { return BASE_URI.'/topics/view?topic_id='.$this->getId(); }

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getVotes()
	{
		$table = new VoteTable();
		return $table->find(['topic_id'=>$this->getId()]);
	}

	/**
	 * @return boolean
	 */
	public function hasVotes()
	{
		return count($this->getVotes()) ? true : false;
	}

	/**
	 * @return VotingRecordList
	 */
	public function getLatestVotingRecords()
	{
		$table = new VoteTable();
		$votes = $table->find(['topic_id'=>$this->getId()], null, false, 1);
		if (count($votes)) {
			$latestVote = $votes[0];
			return $latestVote->getVotingRecords();
		}
		return array();
	}

	/**
	 * @return array An array of Tag Objects with the tag_id as the index
	 */
	public function getTags()
	{
		if (!count($this->tags)) {
			$table = new TagTable();
			$list = $table->find(['topic_id'=>$this->getId()]);
			foreach ($list as $tag) {
				$this->tags[$tag->getId()] = $tag;
			}
		}
		return $this->tags;
	}

	/**
	 * Wipes this topic's tags and replaces it with the given array of tags
	 *
	 * @param array $tags An array of tag_id's to set
	 */
	public function setTags($tags=null)
	{
		$this->tags = [];
		if (count($tags)) {
			foreach ($tags as $tag_id) {
				$tag = new Tag($tag_id);
				$this->tags[$tag->getId()] = $tag;
			}
		}
	}

	/**
	 * Finds out whether this topic has a certain tag
	 * @param Tag $tag The tag to check for
	 * @return boolean
	 */
	public function hasTag(Tag $tag)
	{
		return in_array($tag->getId(),array_keys($this->getTags()));
	}

	private function saveTags()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();

			$query = $zend_db->createStatement('delete from topic_tags where topic_id=?');
			$query->execute([$this->getId()]);

			// NOTICE:
			// Do not call $this->getTags() as it will reload tags from the database
			// Instead, we want to save the set of tags we've made changes to
			$query = $zend_db->createStatement('insert topic_tags set topic_id=?,tag_id=?');
			foreach ($this->tags as $tag) {
				$query->execute([$this->getId(),$tag->getId()]);
			}
		}
	}
}
