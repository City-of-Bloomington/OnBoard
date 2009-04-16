<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Vote extends ActiveRecord
{
	private $id;
	private $date;
	private $voteType_id;
	private $topic_id;
	private $outcome;

	private $voteType;
	private $topic;

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
			$query = $PDO->prepare('select * from votes where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('votes/unknownVote');
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
		if (!$this->voteType_id || !$this->topic_id) {
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
		$fields['date'] = date('Y-m-d',$this->date);
		$fields['voteType_id'] = $this->voteType_id;
		$fields['topic_id'] = $this->topic_id;
		$fields['outcome'] = $this->outcome ? $this->outcome : null;

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

		$sql = "update votes set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert votes set $preparedFields";
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
	 * @return int
	 */
	public function getVoteType_id()
	{
		return $this->voteType_id;
	}

	/**
	 * @return int
	 */
	public function getTopic_id()
	{
		return $this->topic_id;
	}

	/**
	 * @return string
	 */
	public function getOutcome()
	{
		return $this->outcome;
	}

	/**
	 * @return VoteType
	 */
	public function getVoteType()
	{
		if ($this->voteType_id) {
			if (!$this->voteType) {
				$this->voteType = new VoteType($this->voteType_id);
			}
			return $this->voteType;
		}
		return null;
	}

	/**
	 * @return Topic
	 */
	public function getTopic()
	{
		if ($this->topic_id) {
			if (!$this->topic) {
				$this->topic = new Topic($this->topic_id);
			}
			return $this->topic;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

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
	 * @param int $int
	 */
	public function setVoteType_id($int)
	{
		$this->voteType = new VoteType($int);
		$this->voteType_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setTopic_id($int)
	{
		$this->topic = new Topic($int);
		$this->topic_id = $int;
	}

	/**
	 * @param string $string
	 */
	public function setOutcome($string)
	{
		$this->outcome = trim($string);
	}

	/**
	 * @param VoteType $voteType
	 */
	public function setVoteType($voteType)
	{
		$this->voteType_id = $voteType->getId();
		$this->voteType = $voteType;
	}

	/**
	 * @param Topic $topic
	 */
	public function setTopic($topic)
	{
		$this->topic_id = $topic->getId();
		$this->topic = $topic;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Returns just one term's record on this vote
	 * @param Term $term
	 * @return VotingRecord
	 */
	public function getVotingRecord($term)
	{
		$list = new VotingRecordList(array('vote_id'=>$this->id,'term_id'=>$term->getId()));
		if (count($list)) {
			return $list[0];
		}
		else {
			$votingRecord = new VotingRecord();
			$votingRecord->setVote($this);
			$votingRecord->setTerm($term);
			return $votingRecord;
		}
	}

	/**
	 * Returns the set of records matching the result you ask for.  If
	 * no result is given, it returns the full list
	 * @return VotingRecordList
	 */
	public function getVotingRecords($position=null)
	{
		if ($this->id) {
			$fields = array('vote_id'=>$this->id);
			if ($position) { $fields['position'] = $position; }
			return new VotingRecordList($fields);
		}
		return array();
	}

	/**
	 * @return boolean
	 */
	public function hasVotingRecords()
	{
		return count($this->getVotingRecords()) ? true : false;
	}

	/**
	 * @param array $records A POST array of records with term_id as the index
	 */
	public function setVotingRecords(array $records)
	{
		$PDO = Database::getConnection();

		$query = $PDO->prepare('delete from votingRecords where vote_id=?');
		$query->execute(array($this->id));

		$query = $PDO->prepare('insert votingRecords set vote_id=?,term_id=?,position=?');
		foreach ($records as $term_id=>$position) {
			$query->execute(array($this->id,$term_id,$position));
		}
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/topics/viewTopic.php?topic_id='.$this->topic_id;
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		return $this->getTopic()->getCommittee();
	}

	/**
	 * Returns the terms that were current during the time of this vote
	 * @return TermList
	 */
	public function getTerms()
	{
		return $this->getCommittee()->getCurrentTerms($this->date);
	}
}
