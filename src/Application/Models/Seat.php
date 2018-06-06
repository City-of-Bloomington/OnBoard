<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models;

use Application\Models\TermTable;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Seat extends ActiveRecord
{
    public static $types = ['termed', 'open'];
    public static $termIntervals = [
        'P2Y' => '2 years',
        'P1Y' => '1 year',
        'P3Y' => '3 years',
        'P4Y' => '4 years'
    ];

	protected $tablename = 'seats';

	protected $committee;
	protected $appointer;

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
				$sql = 'select * from seats where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('seats/unknownSeat');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setAppointer_id   (1);
			$this->setStartDate(date(DATE_FORMAT));
			$this->setVoting(true);
		}
	}

	/**
	 * We are setting the default Appoint at construct time,
	 * however, the TableGateway contructs first, then calls exchangeArray().
	 * This means there will be a mismatch in the protected $appointer property,
	 * which is intended to be lazy-loaded from $data
	 * We need to clear out that property when loading an array of data
	 */
	public function exchangeArray($data)
	{
		$this->appointer = null; parent::exchangeArray($data);
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
        if (!$this->getType()) { $this->setType('termed'); }

		if (!$this->getName())         { throw new \Exception('missingName'); }
		if (!$this->getCommittee_id()) { throw new \Exception('seats/missingCommittee'); }


		if ($this->getType() === 'termed') {
            if (!$this->getTermLength()) { throw new \Exception('missingTermLength'); }
		}
	}

	public function save()
	{
        if (isset($this->data['endDate'])) { unset($this->data['endDate']); }
        parent::save();
    }

	public function delete()
	{
        if ($this->isSafeToDelete()) {
            parent::delete();
        }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                { return parent::get('id');   }
	public function getType()              { return parent::get('type'); }
	public function getCode()              { return parent::get('code'); }
	public function getName()              { return parent::get('name'); }
	public function getRequirements()      { return parent::get('requirements'); }
	public function getCommittee_id()      { return parent::get('committee_id'); }
	public function getAppointer_id()      { return parent::get('appointer_id'); }
	public function getTermLength()        { return parent::get('termLength'); }
	public function getVoting()            { return parent::get('voting'); }
	public function getTakesApplications() { return parent::get('takesApplications'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getAppointer()    { return parent::getForeignKeyObject(__namespace__.'\Appointer', 'appointer_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setType        ($s) { parent::set('type', $s === 'termed' ? 'termed': 'open'); }
	public function setCode        ($s) { parent::set('code', $s); }
	public function setName        ($s) { parent::set('name', $s); }
	public function setRequirements($s) { parent::set('requirements', $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setAppointer_id($i) { parent::setForeignKeyField (__namespace__.'\Appointer', 'appointer_id', $i); }
	public function setCommittee($o)    { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setAppointer($o)    { parent::setForeignKeyObject(__namespace__.'\Appointer', 'appointer_id', $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setTermLength($s) {
        if ($s) {
            if (array_key_exists($s, self::$termIntervals)) { parent::set('termLength', $s); }
        }
        else { parent::set('termLength', null); }
    }
    public function setVoting           ($b) { $this->data['voting'           ] = $b ? 1 : 0; }
    public function setTakesApplications($b) { $this->data['takesApplications'] = $b ? 1 : 0; }

	public function handleUpdate($post)
	{
		$fields = [
            'code', 'name', 'appointer_id', 'startDate',
            'requirements', 'type', 'termLength', 'voting', 'takesApplications'
        ];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}

	}

	/**
	 * @param string $date
	 */
	public function saveEndDate($date)
	{
        if ($this->getId()) {
            $d = ActiveRecord::parseDate($date, DATE_FORMAT);
            if ($d) {
                // Make sure the end date falls after the start date
                $start = (int)$this->getStartDate('U');
                $end   = (int)$d->format('U');
                if ($end < $start) { throw new \Exception('invalidEndDate'); }

                $updates = [
                    'update terms   set endDate=? where seat_id=? and endDate is null',
                    'update members set endDate=? where seat_id=? and endDate is null',
                    'update seats   set endDate=? where id=?',
                ];
                $params = [
                    $d->format(ActiveRecord::MYSQL_DATE_FORMAT),
                    $this->getId()
                ];

                $zend_db = Database::getConnection();
                $zend_db->getDriver()->getConnection()->beginTransaction();
                try {
                    foreach ($updates as $sql) { $zend_db->query($sql)->execute($params); }
                    $zend_db->getDriver()->getConnection()->commit();
                }
                catch (\Exception $e) {
                    $zend_db->getDriver()->getConnection()->rollback();
                    throw $e;
                }
            }
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function getData() { return $this->data; }

	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
        $sql = "select count(*) as count from (
                    select id from terms where seat_id=?
                    union
                    select id from members where seat_id=?
                ) foreignKeys";
        $zend_db = Database::getConnection();
        $result = $zend_db->query($sql, [$this->getId(), $this->getId()]);
        if ($result) {
            $row = $result->current();
            return (int)$row['count'] === 0 ? true : false;
        }
	}

	/**
	 * @return Zend\Db\Result
	 */
	public function getMembers()
	{
		$table = new MemberTable();
		return $table->find(['seat_id'=>$this->getId()]);
	}

	/**
	 * @return Member
	 */
	public function getCurrentMember()
	{
        $table = new MemberTable();
        $list = $table->find(['seat_id'=>$this->getId(), 'current'=>true]);
        if (count($list)) {
            return $list->current();
        }
	}

	/**
	 * Returns the most recent member for this seat
	 *
	 * @return Member
	 */
	public function getLatestMember()
	{
        $table = new MemberTable();
        $list = $table->find(['seat_id'=>$this->getId()], 'startDate desc', false, 1);
        if (count($list)) {
            return $list->current();
        }
	}

	/**
	 * Factory function for instantiating a new member
	 *
	 * Instantiates a Member object that is valid for this seat
	 *
	 * @return Member
	 */
	public function newMember()
	{
        if ($this->getType() === 'open') {
            $member = new Member();
            $member->setSeat($this);
            $member->setCommittee_id($this->getCommittee_id());
            return $member;
        }

        throw new \Exception('seats/invalidMember');
	}

	/**
	 * @return array
	 */
	public function getTerms()
	{
		$search = ['seat_id' => $this->getId()];

		$table = new TermTable();
		$list = $table->find($search);

		$terms = [];
		foreach ($list as $t) { $terms[] = $t; }
		return $terms;
	}

	/**
	 * Returns the term for a seat at a given point in time
	 *
	 * If no timestamp is provided, returns the current term
	 *
	 * @TODO Generate previous terms for timestamps in the past
	 * @param int $timestamp
	 */
	public function getTerm($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        $table = new TermTable();

        $list = $table->find(['seat_id'=>$this->getId(), 'current'=>$timestamp]);
        if (count($list)) {
            return $list->current();
        }
        else {
            if ($timestamp >= time()) {
                // Generate the next term in the sequence
                $latestTerm = $this->getLatestTerm();

                if ($latestTerm) {
                    $newTerm = $this->generateTermForTimestamp($latestTerm, $timestamp);
                    if ($newTerm) {
                        $newTerm->save();
                        return $newTerm;
                    }
                }
            }
            else {
                // Generate the previous term in the sequence

            }
        }
	}

	/**
	 * Factory function for creating a Term for a particular time
	 *
	 * This function only instantiates a valid Term object.  It does
	 * not save the new term in the database.
	 *
	 * @TODO Generate terms for timestamps in the past
	 * @param Term $latestTerm The most recent term in the database
	 * @param int $timestamp   The target timestamp
	 * @return Term  A newly created Term that has not been saved in the database
	 */
	public function generateTermForTimestamp(Term $latestTerm, $timestamp)
	{
        $c = 0;
        $maxIterations = 3;

        while ($c < $maxIterations) {
            // Check to if the latest term will work for the target time
            if (   $timestamp > $latestTerm->getStartDate('U')
                && $timestamp < $latestTerm->getEndDate('U')) {

                return $latestTerm;
            }
            else {
                $latestTerm = ($timestamp > $latestTerm->getEndDate('U'))
                    ? $latestTerm->generateNextTerm()
                    : $latestTerm->generatePreviousTerm();
                $c++;
            }
        }
	}

	/**
	 * Returns the most recent term in the database
	 *
	 * @return Term
	 */
	public function getLatestTerm()
	{
        $table = new TermTable();
        $list = $table->find(['seat_id'=>$this->getId()], 'startDate desc', false, 1);
        if (count($list)) {
            return $list->current();
        }
	}

	/**
	 * Checks whether it is safe to delete a seat.
	 *
     * Seats on 'seated' committees can only be deleted if there are no terms
     * associated with the seat.
     *
     * Open committees do not need seats.
     * When you change a committee's type from seated to open, the seats
     * will still be there.  For 'open' committees we allow users to delete the
     * unneeded seats.  The system will preserve the terms.  We simply remove the
     * seat_id from all the terms before deleting the seat.
     *
	 * @return bool
	 */
	public function canBeDeleted()
	{
        $committee = $this->getCommittee();

        if ($committee->getType() === 'seated') {
            // Seats on seated committees can only be deleted if there are no terms
            $zend_db = Database::getConnection();
            $sql = 'select count(*) as count from terms where seat_id=?';
            $result = $zend_db->query($sql, [$this->getId()]);
            $row = $result->current();
            return $row['count'] === 0;
        }
        else {
            // Terms for open committees do not need seats.
            return true;
        }
	}

	/**
	 * @return boolean
	 */
	public function hasVacancy($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        // Seats cannot be vacant if they're not active anymore
        if (!$this->getEndDate() || $this->getEndDate('U') > $timestamp) {
            if ($this->getType() === 'termed') {
                $term = $this->getTerm($timestamp);
                if ($term) {
                    return $term->getMember($timestamp) ? false : true;
                }
                // If the seat is active, and there's no current term
                // there probably should be.  It means that no one is serving
                // the current term
                return true;
            }
            else {
                return $this->getCurrentMember() ? false : true;
            }
        }

        return false;
	}

	public function isVoting()         : bool { return $this->getVoting()            ? true : false; }
	public function takesApplications(): bool { return $this->getTakesApplications() ? true : false; }
}
