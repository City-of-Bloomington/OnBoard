<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Topic extends ActiveRecord
{
	private $id;
	private $topicType_id;
	private $date;
	private $number;
	private $description;
	private $synopsis;
	private $committee_id;

	private $topicType;
	private $committee;
	private $tags = array();

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from topics where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('topics/unknownTopic');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					switch ($field) {
						case 'date':
							if ($value && $value!='0000-00-00') {
								$this->date = strtotime($value);
							}
							break;
						default:
							$this->$field = $value;
					}
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->date = time();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->topicType_id || !$this->number || !$this->description
			|| !$this->synopsis || !$this->committee_id) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->date) {
			$this->date = time();
		}
	}

	/**
	 * Saves this record back to the database
	 *
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['topicType_id'] = $this->topicType_id;
		$fields['date'] = date('Y-m-d',$this->date);
		$fields['number'] = $this->number;
		$fields['description'] = $this->description;
		$fields['synopsis'] = $this->synopsis;
		$fields['committee_id'] = $this->committee_id;

		// Split the fields up into a preparedFields array and a values array.
		// PDO->execute cannot take an associative array for values, so we have
		// to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value) {
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) {
			$this->update($values,$preparedFields);
		}
		else {
			$this->insert($values,$preparedFields);
		}
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update topics set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert topics set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getTopicType_id()
	{
		return $this->topicType_id;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getDate($format=null)
	{
		if ($format && $this->date) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->date);
			}
			else {
				return date($format,$this->date);
			}
		}
		else {
			return $this->date;
		}
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @return text
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return text
	 */
	public function getSynopsis()
	{
		return $this->synopsis;
	}

	/**
	 * @return int
	 */
	public function getCommittee_id()
	{
		return $this->committee_id;
	}

	/**
	 * @return TopicType
	 */
	public function getTopicType()
	{
		if ($this->topicType_id) {
			if (!$this->topicType) {
				$this->topicType = new TopicType($this->topicType_id);
			}
			return $this->topicType;
		}
		return null;
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		if ($this->committee_id) {
			if (!$this->committee) {
				$this->committee = new Committee($this->committee_id);
			}
			return $this->committee;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param int $int
	 */
	public function setTopicType_id($int)
	{
		$this->topicType = new TopicType($int);
		$this->topicType_id = $int;
	}

	/**
	 * Sets the date
	 *
	 * Dates and times should be stored as timestamps internally.
	 * This accepts dates and times in multiple formats and sets the internal timestamp
	 * Accepted formats are:
	 * 		array - in the form of PHP getdate()
	 *		timestamp
	 *		string - anything strtotime understands
	 * @param date $date
	 */
	public function setDate($date)
	{
		if (is_array($date)) {
			$this->date = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->date = $date;
		}
		else {
			$this->date = strtotime($date);
		}
	}

	/**
	 * @param string $string
	 */
	public function setNumber($string)
	{
		$this->number = trim($string);
	}

	/**
	 * @param text $text
	 */
	public function setDescription($text)
	{
		$this->description = $text;
	}

	/**
	 * @param text $text
	 */
	public function setSynopsis($text)
	{
		$this->synopsis = $text;
	}

	/**
	 * @param int $int
	 */
	public function setCommittee_id($int)
	{
		$this->committee = new Committee($int);
		$this->committee_id = $int;
	}

	/**
	 * @param TopicType $topicType
	 */
	public function setTopicType($topicType)
	{
		$this->topicType_id = $topicType->getId();
		$this->topicType = $topicType;
	}

	/**
	 * @param Committee $committee
	 */
	public function setCommittee($committee)
	{
		$this->committee_id = $committee->getId();
		$this->committee = $committee;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/topics/viewTopic.php?topic_id='.$this->id;
	}

	/**
	 * @return VoteList
	 */
	public function getVotes()
	{
		if ($this->id) {
			return new VoteList(array('topic_id'=>$this->id));
		}
		return array();
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
		$votes = $this->getVotes();
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
			$list = new TagList(array('topic_id'=>$this->id));
			foreach ($list as $tag) {
				$this->tags[$tag->getId()] = $tag;
			}
		}
		return $this->tags;
	}
	/**
	 * Wipes this topic's tags and replaces it with the given array of tags
	 * @param array $tags An array of tag_id's to set
	 */
	public function setTags($tags=null)
	{
		$this->tags = array();
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
		$pdo = Database::getConnection();

		$query = $pdo->prepare('delete from topic_tags where topic_id=?');
		$query->execute(array($this->id));

		// NOTICE:
		// Do not call $this->getTags() as it will reload tags from the database
		// Instead, we want to save the set of tags we've made changes to
		$query = $pdo->prepare('insert topic_tags set topic_id=?,tag_id=?');
		foreach ($this->tags as $tag) {
			$query->execute(array($this->id,$tag->getId()));
		}
	}
}
