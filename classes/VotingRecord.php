<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class VotingRecord extends ActiveRecord
{
	private $id;
	private $term_id;
	private $vote_id;
	private $position;

	private $term;
	private $vote;

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
			$query = $PDO->prepare('select * from votingRecords where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('votingRecords/unknownVotingRecord');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					$this->$field = $value;
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->position = 'absent';
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->term_id || !$this->vote_id) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->position) {
			$this->position = 'absent';
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
		$fields['term_id'] = $this->term_id;
		$fields['vote_id'] = $this->vote_id;
		$fields['position'] = $this->position;

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

		$sql = "update votingRecords set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert votingRecords set $preparedFields";
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
	public function getTerm_id()
	{
		return $this->term_id;
	}

	/**
	 * @return int
	 */
	public function getVote_id()
	{
		return $this->vote_id;
	}

	/**
	 * @return string
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @return Term
	 */
	public function getTerm()
	{
		if ($this->term_id) {
			if (!$this->term) {
				$this->term = new Term($this->term_id);
			}
			return $this->term;
		}
		return null;
	}

	/**
	 * @return Vote
	 */
	public function getVote()
	{
		if ($this->vote_id) {
			if (!$this->vote) {
				$this->vote = new Vote($this->vote_id);
			}
			return $this->vote;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param int $int
	 */
	public function setTerm_id($int)
	{
		$this->term = new Term($int);
		$this->term_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setVote_id($int)
	{
		$this->vote = new Vote($int);
		$this->vote_id = $int;
	}

	/**
	 * @param string $string
	 */
	public function setPosition($string)
	{
		$this->position = trim($string);
	}

	/**
	 * @param Term $term
	 */
	public function setTerm($term)
	{
		$this->term_id = $term->getId();
		$this->term = $term;
	}

	/**
	 * @param Vote $vote
	 */
	public function setVote($vote)
	{
		$this->vote_id = $vote->getId();
		$this->vote = $vote;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getDate($format=null)
	{
		return $this->getVote()->getDate($format);
	}

	/**
	 * @return Topic
	 */
	public function getTopic() {
		return $this->getVote()->getTopic();
	}

	/**
	 * @return array
	 */
	public static function getPossiblePositions()
	{
		return array('yes','no','abstain','absent');
	}

	/**
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->getTerm()->getPerson();
	}

	/**
	 * @return VoteType
	 */
	public function getVoteType()
	{
		return $this->getVote()->getVoteType();
	}
}
