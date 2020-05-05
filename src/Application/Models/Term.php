<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

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
				$db = Database::getConnection();
				$sql = 'select * from terms where id=?';

				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
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

		// Make sure this term is not overlapping terms for the seat
		$db = Database::getConnection();
		$sql = "select id from terms
                where seat_id=?
                and (?<endDate and ?>startDate)";
		if ($this->getId()) { $sql.= ' and id!='.$this->getId(); }

		$result = $db->createStatement($sql)->execute([
            $this->getSeat_id(),
			$this->getStartDate(), $this->getEndDate()
		]);
		if (count($result) > 0) {
			throw new \Exception('terms/overlappingTerms');
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
	public function getStartDate(?string $format=null) { return parent::getDateData('startDate', $format); }
	public function getEndDate  (?string $format=null) { return parent::getDateData('endDate',   $format); }

	public function setSeat_id     ($i) { parent::setForeignKeyField (__namespace__.'\Seat', 'seat_id', $i); }
	public function setSeat        ($o) { parent::setForeignKeyObject(__namespace__.'\Seat', 'seat_id', $o); }
	public function setStartDate(?string $date, ?string $format='Y-m-d') { parent::setDateData('startDate', $date, $format); }
	public function setEndDate  (?string $date, ?string $format='Y-m-d') { parent::setDateData('endDate',   $date, $format); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function getData() { return $this->data; }

	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
        $sql = 'select count(*) as count from members where term_id=?';
        $db = Database::getConnection();
        $result = $db->query($sql, [$this->getId()]);
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
	 * @return Laminas\Db\Result
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
            $db = Database::getConnection();

            $sql = 'select count(*) as count from members where endDate is null and term_id=?';
            $result = $db->query($sql, [$this->getId()]);
            $row = $result->current();
            if ($row['count'] > 0) {
                return false;
            }

            $sql = 'select max(endDate) as endDate from members where term_id=?';
            $result = $db->query($sql, [$this->getId()]);
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

        return $seat->getTerm((int)$d->format('U'));
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
        $oneDay     = new \DateInterval('P1D');

        $s = new \DateTime($this->getStartDate());
        $e = new \DateTime($this->getEndDate());

        $start = $e->add($oneDay    )->format(DATE_FORMAT);

        $e->add($termLength);
        $e->sub($oneDay);
        $end = $e->format(DATE_FORMAT);

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
        $oneDay     = new \DateInterval('P1D');

        $s = new \DateTime($this->getStartDate());
        $e = new \DateTime($this->getEndDate());

        $end = $s->sub($oneDay)->format(DATE_FORMAT);

        $s->sub($termLength);
        $s->add($oneDay);
        $start = $s->format(DATE_FORMAT);

        $term = new Term();
        $term->setStartDate($start);
        $term->setEndDate  ($end);
        $term->setSeat($seat);
        return $term;
	}
}
