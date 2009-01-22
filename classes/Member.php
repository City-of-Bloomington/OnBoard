<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class Member extends ActiveRecord
{
	private $id;
	private $seat_id;
	private $user_id;
	private $term_start;
	private $term_end;

	private $seat;
	private $user;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 */
	public function __construct($id=null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();
			$sql = "select id,seat_id,user_id,
					unix_timestamp(term_start) as term_start,
					unix_timestamp(term_end) as term_end
					from members where id=?";
			$query = $PDO->prepare($sql);
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('members/unknownMember'); }
			foreach ($result[0] as $field=>$value) { if ($value) $this->$field = $value; }
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
			$this->term_start = time();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		# Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->seat_id || !$this->user_id) { throw new Exception('missingRequiredFields'); }
		if (!$this->term_start) { $this->term_start = time(); }

		# Make sure this member does not exceed the maxCurrentMembers for the seat
		if ( $this->term_start <= time() &&
			 (!$this->term_end || $this->term_end >= time()) )
		{
			# The member we're adding is current, make sure there's room
			$count = count($this->getSeat()->getCurrentMembers());
			if (!$this->id) { $count++; }
			if ($count > $this->getSeat()->getMaxCurrentMembers())
			{
				throw new Exception('seats/maxCurrentMembersFilled');
			}
		}
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
		$fields['seat_id'] = $this->seat_id;
		$fields['user_id'] = $this->user_id;
		$fields['term_start'] = $this->term_start ? date('Y-m-d',$this->term_start) : date('Y-m-d');
		$fields['term_end'] = $this->term_end ? date('Y-m-d',$this->term_end) : null;

		# Split the fields up into a preparedFields array and a values array.
		# PDO->execute cannot take an associative array for values, so we have
		# to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value)
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

		$sql = "update members set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert members set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	public function delete()
	{
		if ($this->id)
		{
			$PDO = Database::getConnection();

			$query = $PDO->prepare('delete from votingRecords where member_id=?');
			$query->execute(array($this->id));

			$query = $PDO->prepare('delete from members where id=?');
			$query->execute(array($this->id));
		}
	}

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getSeat_id() { return $this->seat_id; }
	public function getUser_id() { return $this->user_id; }

	public function getTerm_start($format=null)
	{
		if ($format && $this->term_start)
		{
			if (strpos($format,'%')!==false) { return strftime($format,$this->term_start); }
			else { return date($format,$this->term_start); }
		}
		else return $this->term_start;
	}

	public function getTerm_end($format=null)
	{
		if ($format && $this->term_end)
		{
			if (strpos($format,'%')!==false) { return strftime($format,$this->term_end); }
			else { return date($format,$this->term_end); }
		}
		else return $this->term_end;
	}

	public function getSeat()
	{
		if ($this->seat_id)
		{
			if (!$this->seat) { $this->seat = new Seat($this->seat_id); }
			return $this->seat;
		}
		else return null;
	}

	public function getUser()
	{
		if ($this->user_id)
		{
			if (!$this->user) { $this->user = new User($this->user_id); }
			return $this->user;
		}
		else return null;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setSeat_id($int) { $this->seat = new Seat($int); $this->seat_id = $int; }
	public function setUser_id($int) { $this->user = new User($int); $this->user_id = $int; }
	public function setSeat($seat) { $this->seat_id = $seat->getId(); $this->seat = $seat; }
	public function setUser($user) { $this->user_id = $user->getId(); $this->user = $user; }

	public function setTerm_start($date)
	{
		if (is_array($date)) { $this->term_start = $this->dateArrayToTimestamp($date); }
		elseif(ctype_digit($date)) { $this->term_start = $date; }
		else { $this->term_start = strtotime($date); }
	}

	public function setTerm_end($date)
	{
		if (is_array($date)) { $this->term_end = $this->dateArrayToTimestamp($date); }
		elseif(ctype_digit($date)) { $this->term_end = $date; }
		else { $this->term_end = strtotime($date); }
	}

	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	public function getFirstname() { return $this->getUser()->getFirstname(); }
	public function getLastname() { return $this->getUser()->getLastname(); }

	public function getFullname() {
		return $this->getUser()->getFirstname()." ".$this->getUser()->getLastname();
	}

	public function getURL()
	{
		return BASE_URL.'/members/viewMember.php?member_id='.$this->id;
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		return $this->getSeat()->getCommittee();
	}

	public function getAccordancePercentage($otherMember)
	{
		return VotingRecordList::findAccordancePercentage($this,$otherMember);
	}
	/**
	 * Calculates a member's voting record percentages for each of their
	 * possible responses (yes, no, absent, abstain).
	 * If you pass in a TopicType, it will calculate the percentages only
	 * for votes on topics of the type you pass in.
	 * @param TopicType|int $topicType The Type of Topics to look at
	 * @return array
	 */
	public function getVotePercentages($topicType=null)
	{
		$search = array('member_id'=>$this->id);
		if ($topicType)
		{
			if (ctype_digit($topicType)) { $topicType = new TopcType($topicType); }
			$search['topicType'] = $topicType;
		}
		$votingRecords = new VotingRecordList($search);
		$total = count($votingRecords) ? count($votingRecords) : 1;

		$output = array('yes'=>0,'no'=>0,'abstain'=>0,'absent'=>0);
		foreach (array_keys($output) as $memberVote)
		{
			$search = array('member_id'=>$this->id,'memberVote'=>$memberVote);
			if ($topicType) { $search['topicType'] = $topicType; }
			$count = count(new VotingRecordList($search));
			$output[$memberVote] = round(($count * 100 / $total),2);
		}
		return $output;
	}

	/**
	 * @return boolean
	 */
	public function hasVotingRecord()
	{
		return count($this->getUser()->getVotingRecords()) ? true : false;
	}
}
