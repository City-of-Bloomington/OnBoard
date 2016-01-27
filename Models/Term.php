<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Term extends ActiveRecord
{
	protected $tablename = 'terms';

	protected $seat;

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
				$sql = 'select * from terms where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('terms/unknownTerm');
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
		if (!$this->getStartDate()) {
			 $this->setStartDate(date(DATE_FORMAT));
		}

        $seat = $this->getSeat();
        if (!$seat) { throw new \Exception('terms/missingSeat'); }

        // Create a valid endDate if there isn't one already
        $termLength = new \DateInterval($seat->getTermLength());
        $oneDay     = new \DateInterval('P1D');
		if (!$this->getEndDate()) {
            $s = new \DateTime($this->getStartDate());
            $s->add($termLength);
            $s->sub($oneDay);
            $this->setEndDate($s->format(DATE_FORMAT));
        }

		// Make sure the endDate is valid
		$s = new \DateTime($this->getStartDate());
		$s->add($termLength);
		$s->sub($oneDay);
		if ($s->format(DATE_FORMAT) != $this->getEndDate(DATE_FORMAT)) {
            throw new \Exception('invalidEndDate');
		}

		// Make sure this term is not overlapping terms for the seat
		$zend_db = Database::getConnection();
		$sql = "select id from terms
                where seat_id=?
                and (?<endDate and ?>startDate)";
		if ($this->getId()) { $sql.= ' and id!='.$this->getId(); }

		$result = $zend_db->createStatement($sql)->execute([
            $this->getSeat_id(),
			$this->getStartDate(), $this->getEndDate()
		]);
		if (count($result) > 0) {
			throw new \Exception('overlappingTerms');
		}
	}

	public function save() { parent::save(); }

	public function delete()
	{
        if ($this->isSafeToDelete()) {
            parent::delete();
        }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getSeat_id()      { return parent::get('seat_id'     ); }
	public function getSeat()         { return parent::getForeignKeyObject(__namespace__.'\Seat', 'seat_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setSeat_id     ($i) { parent::setForeignKeyField (__namespace__.'\Seat', 'seat_id', $i); }
	public function setSeat        ($o) { parent::setForeignKeyObject(__namespace__.'\Seat', 'seat_id', $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
        $fields = ['seat_id', 'startDate', 'endDate'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
        $sql = 'select count(*) as count from members where term_id=?';
        $zend_db = Database::getConnection();
        $result = $zend_db->query($sql, [$this->getId()]);
        if ($result) {
            $row = $result->current();
            return ((int)$row['count'] === 0) ? true : false;
        }
	}

	/**
	 * @return Member
	 */
	public function newMember()
	{
        $seat = $this->getSeat();

        $member = new Member();
        $member->setTerm($this);
        $member->setSeat($seat);
        $member->setCommittee_id($seat->getCommittee_id());

        return $member;
	}

	/**
	 * @param int $timestamp
	 * @return Member
	 */
	public function getMember($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }
        $table = new MemberTable();
        $list = $table->find(['term_id'=>$this->getId(), 'current'=>$timestamp]);
        if (count($list)) {
            return $list->current();
        }
	}

	/**
	 * @return Zend\Db\Result
	 */
	public function getMembers()
	{
        $table = new MemberTable();
        return $table->find(['term_id'=>$this->getId()]);
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
        return $this->getSeat()->getCommittee();
	}

	/**
	 * @return boolean
	 */
	public function isVacant()
	{
        if ($this->getId()) {
            $zend_db = Database::getConnection();

            $sql = 'select count(*) as count from members where endDate is null and term_id=?';
            $result = $zend_db->query($sql, [$this->getId()]);
            $row = $result->current();
            if ($row['count'] > 0) {
                return false;
            }

            $sql = 'select max(endDate) as endDate from members where term_id=?';
            $result = $zend_db->query($sql, [$this->getId()]);
            if (count($result)) {
                $row = $result->current();
                if ($row['endDate']) {
                    $endDate = new \DateTime($row['endDate']);
                    return (int)$endDate->format('U') < (int)$this->getEndDate('U');
                }

                // No max(endDate) for members means there are no members
                return true;
            }
        }
        return false;
	}

	/**
	 * @return Term
	 */
	public function getNextTerm()
	{
        $seat = $this->getSeat();

        $twoDays = new \DateInterval('P2D');

        $d = new \DateTime($this->getEndDate());
        $d->add($twoDays);

        return $seat->getTerm($d->format('U'));
	}

	/**
	 * Returns a Term object for the next in the series
	 *
	 * This term object is not, yet saved in the database
	 *
	 * @return Term
	 */
	public function generateNextTerm()
	{
        $seat = $this->getSeat();
        $termLength = new \DateInterval($seat->getTermLength());

        $s = new \DateTime($this->getStartDate());
        $e = new \DateTime($this->getEndDate());

        $start = $s->add($termLength)->format(DATE_FORMAT);
        $end   = $e->add($termLength)->format(DATE_FORMAT);

        $term = new Term();
        $term->setStartDate($start);
        $term->setEndDate  ($end);
        $term->setSeat($seat);
        return $term;
	}

	/**
	 * Returns a Term object for the previous one in the series
	 *
	 * This term object is not, yet saved in the database.
	 *
	 * @return Term
	 */
	public function generatePreviousTerm()
	{
        $seat = $this->getSeat();
        $termLength = new \DateInterval($seat->getTermLength());

        $s = new \DateTime($this->getStartDate());
        $e = new \DateTime($this->getEndDate());

        $start = $s->sub($termLength)->format(DATE_FORMAT);
        $end   = $e->sub($termLength)->format(DATE_FORMAT);

        $term = new Term();
        $term->setStartDate($start);
        $term->setEndDate  ($end);
        $term->setSeat($seat);
        return $term;
	}

	/**
	 * @param int $timestamp
	 * @return boolean
	 */
	public function isInEndWarningPeriod($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        $days = $this->getCommittee()->getTermEndWarningDays();
        if ($days) {
            $periodEnd = strtotime("+$days days", $timestamp);
            $termEnd = (int)$this->getEndDate('U');

            return $periodEnd > $termEnd && $timestamp < $termEnd;
        }
	}
}