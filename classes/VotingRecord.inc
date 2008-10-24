<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class VotingRecord extends ActiveRecord
{
	private $id;
	private $member_id;
	private $vote_id;
	private $memberVote;

	private $vote;
	private $member;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 */
	public function __construct($id=null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from votingRecords where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('votingRecords/unknownVotingRecord'); }
			foreach($result[0] as $field=>$value) { if ($value) $this->$field = $value; }
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
			$this->memberVote = 'absent';
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		# Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->member_id || !$this->vote_id)
		{
			throw new Exception('missingRequiredFields');
		}

		if (!$this->memberVote) { $this->meberVote = 'absent'; }
	}
	/**
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['member_id'] = $this->member_id;
		$fields['vote_id'] = $this->vote_id;
		$fields['memberVote'] = $this->memberVote;

		# Split the fields up into a preparedFields array and a values array.
		# PDO->execute cannot take an associative array for values, so we have
		# to strip out the keys from $fields
		$preparedFields = array();
		foreach($fields as $key=>$value)
		{
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) { $this->update($values,$preparedFields); }
		else { $this->insert($values,$preparedFields); }
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

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getMember_id() { return $this->member_id; }
	public function getVote_id() { return $this->vote_id; }
	public function getMemberVote() { return $this->memberVote; }

	public function getMember()
	{
		if ($this->member_id)
		{
			if (!$this->member) { $this->member = new Member($this->member_id); }
			return $this->member;
		}
		else return null;
	}

	public function getVote()
	{
		if ($this->vote_id)
		{
			if (!$this->vote) { $this->vote = new Vote($this->vote_id); }
			return $this->vote;
		}
		else return null;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setMember_id($int) { $this->member = new Member($int); $this->member_id = $int; }
	public function setVote_id($int) { $this->vote = new Vote($int); $this->vote_id = $int; }
	public function setMemberVote($string) { $this->memberVote = trim($string); }

	public function setMember($member) { $this->member_id = $member->getId(); $this->member = $member; }
	public function setVote($vote) { $this->vote_id = $vote->getId(); $this->vote = $vote; }

	public function getCommittee_id(){
		$vote = $this->getVote();
		$topic = $vote->getTopic()->getCommittee_id();
		return $topic->getCommittee_id();
	}

	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	public function getDate($format=null)
	{
		return $this->getVote()->getDate($format);
	}

	public function getTopic() { return $this->getVote()->getTopic(); }
}
