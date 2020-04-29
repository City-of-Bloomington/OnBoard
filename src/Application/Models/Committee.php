<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\View;
use Web\Database;

class Committee extends ActiveRecord
{
    public static $types = ['seated', 'open'];

	protected $tablename = 'committees';
	protected $departments = [];
	private $departmentsHaveChanged = false;

	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$db = Database::getConnection();
				$sql = 'select * from committees where id=?';
				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('committees/unknownCommittee');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setType('seated');
			$this->setTermEndWarningDays(TERM_END_WARNING_DEFAULT);
			$this->setApplicationLifetime(APPLICATION_LIFETIME_DEFAULT);
		}
	}

	public function validate()
	{
        if (!$this->getType()) { $this->setType('seated'); }
		if (!$this->getName()) { throw new \Exception('missingName'); }
	}

	public function save()
	{
        // endDate should never be altered during a normal save
        if (isset($this->data['endDate'])) {
            unset($this->data['endDate']);
        }
        parent::save();

        if ($this->departmentsHaveChanged) {
            $db = Database::getConnection();
            $sql = 'delete from committee_departments where committee_id=?';
            $db->query($sql, [$this->getId()]);

            $sql = 'insert committee_departments set committee_id=?,department_id=?';
            $insert = $db->createStatement($sql);
            foreach (array_keys($this->departments) as $id) {
                $params = [$this->getId(), $id];
                try { $insert->execute($params); }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                { return parent::get('id');               }
	public function getType()              { return parent::get('type');             }
	public function getName()              { return parent::get('name');             }
	public function getStatutoryName()     { return parent::get('statutoryName');    }
	public function getCode()              { return parent::get('code');             }
	public function getCalendarId()        { return parent::get('calendarId');       }
	public function getWebsite()           { return parent::get('website');          }
	public function getVideoArchive()      { return parent::get('videoArchive');     }
	public function getEmail()             { return parent::get('email');            }
	public function getPhone()             { return parent::get('phone');            }
    public function getAddress()           { return parent::get('address');          }
    public function getCity()              { return parent::get('city');             }
    public function getState()             { return parent::get('state');            }
    public function getZip()               { return parent::get('zip');              }
	public function getDescription()       { return parent::get('description');      }
	public function getYearFormed()        { return parent::get('yearFormed');       }
	public function getMeetingSchedule()   { return parent::get('meetingSchedule');  }
	public function getTermEndWarningDays()  { return parent::get('termEndWarningDays'); }
	public function getApplicationLifetime() { return parent::get('applicationLifetime'); }
	public function getEndDate($f=null)    { return parent::getDateData('endDate', $f); }
	public function getLegislative() { return parent::get('legislative') ? true : false; }

	public function setType($s) { parent::set('type', $s === 'seated' ? 'seated': 'open'); }
	public function setName            ($s) { parent::set('name',             $s); }
	public function setStatutoryName   ($s) { parent::set('statutoryName',    $s); }
	public function setCode            ($s) { parent::set('code',             $s); }
	public function setWebsite         ($s) { parent::set('website',          $s); }
	public function setVideoArchive    ($s) { parent::set('videoArchive',     $s); }
	public function setCalendarId      ($s) { parent::set('calendarId',       $s); }
    public function setEmail           ($s) { parent::set('email',            $s); }
    public function setPhone           ($s) { parent::set('phone',            $s); }
    public function setAddress         ($s) { parent::set('address',          $s); }
    public function setCity            ($s) { parent::set('city',             $s); }
    public function setState           ($s) { parent::set('state',            $s); }
    public function setZip             ($s) { parent::set('zip',              $s); }
	public function setDescription     ($s) { parent::set('description',      $s); }
	public function setYearFormed      ($s) { parent::set('yearFormed',  (int)$s); }
	public function setMeetingSchedule ($s) { parent::set('meetingSchedule',  $s); }
	public function setTermEndWarningDays ($s) { parent::set('termEndWarningDays',  (int)$s); }
	public function setApplicationLifetime($s) { parent::set('applicationLifetime', (int)$s); }
	public function setLegislative($b) { $this->data['legislative'] = $b ? 1 : 0; }

	/**
	 * @param array $post The POST request
	 */
	public function handleUpdate($post)
	{
        if (!isset($post['departments'])) { $post['departments'] = null; }

		$fields = [
            'type', 'departments',
			'name', 'statutoryName', 'code', 'website', 'videoArchive', 'yearFormed', 'calendarId',
			'email', 'phone', 'address', 'city', 'state', 'zip',
			'description', 'meetingSchedule',
			'termEndWarningDays', 'applicationLifetime', 'legislative'
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
                $db = Database::getConnection();

                $params = [
                    $d->format(ActiveRecord::MYSQL_DATE_FORMAT),
                    $this->getId()
                ];

                $updates = [
                    "update terms t join seats s on t.seat_id=s.id
                                         set t.endDate=? where s.committee_id=? and t.endDate is null",
                    'update applications set archived=?  where committee_id=?   and archived  is null',
                    'update offices      set endDate=?   where committee_id=?   and endDate   is null',
                    'update seats        set endDate=?   where committee_id=?   and endDate   is null',
                    'update members      set endDate=?   where committee_id=?   and endDate   is null',
                    'update committees   set endDate=?   where id=?'
                ];
                $db->getDriver()->getConnection()->beginTransaction();
                try {
                    foreach ($updates as $sql) {
                        $db->query($sql)->execute($params);
                    }
                    $db->getDriver()->getConnection()->commit();
                }
                catch (\Exception $e) {
                    $db->getDriver()->getConnection()->rollback();
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
	 * @return Member
	 */
	public function newMember()
	{
        if ($this->getType() === 'open') {
            $member = new Member();
            $member->setCommittee($this);
            return $member;
        }

        throw new \Exception('committees/invalidMember');
	}

	/**
	 * Returns members for the committee
	 *
	 * @param array $fields
	 * @return Laminas\Db\ResultSet
	 */
	public function getMembers(array $fields=null)
	{
        $fields['committee_id'] = $this->getId();

		$table = new MemberTable();
		return $table->find($fields);
	}

	/**
	 * @param string $date
	 * @return Laminas\Db\Result
	 */
	public function getOffices($date=null)
	{
        $search = ['committee_id'=>$this->getId()];
        if (!empty($date)) { $search['current'] = $date; }

        $table = new OfficeTable();
        return $table->find($search);
	}

	/**
	 * Returns a ResultSet containing Seats.
	 *
	 * @param array $fields
	 * @return Laminas\Db\ResultSet A ResultSet containing the Seat objects
	 */
	public function getSeats(array $fields=null)
	{
        $fields['committee_id'] = $this->getId();

		$table = new SeatTable();
		return $table->find($fields);
	}

    /**
     * Internal function to check for past records
     *
     * @param string $table
     * @return boolean
     */
    private function hasPast($table)
	{
        $sql = "select count(*) as count from $table
                where committee_id=?
                  and endDate is not null and endDate < now()";
        $db = Database::getConnection();
        $result = $db->query($sql)->execute([$this->getId()]);
        $row = $result->current();
        return $row['count'] ? true : false;
	}
	public function hasPastSeats  () { return $this->hasPast('seats'  ); }
	public function hasPastMembers() { return $this->hasPast('members'); }

	/**
	 * Returns terms that were current for the given timestamp.
	 * If no timestamp is given, the current time is used.
	 *
	 * @param timestamp $timestamp The timestamp for when the terms would have been current
	 *
	 * @return Laminas\Db\ResultSet
	 */
	public function getCurrentTerms($timestamp=null)
	{
		if (!$timestamp) {
			$timestamp = time();
		}
		$terms = new TermTable();
		return $terms->find(['committee_id'=>$this->getId(), 'current'=>$timestamp]);
	}

	/**
	 * @return boolean
	 */
	public function hasVacancy()
	{
        if ($this->getType() === 'seated') {
            $seats = $this->getSeats(['current'=>true]);
            foreach ($seats as $s) {
                if ($s->hasVacancy()) { return true; }
            }
        }
        return false;
	}

	/**
	 * @return int
	 */
	public function getVacancyCount()
	{
        $c = 0;
        if ($this->getType() === 'seated') {
            $seats = $this->getSeats(['current'=>true]);
            foreach ($seats as $s) {
                if ($s->hasVacancy()) { $c++; }
            }
        }
        return $c;
	}

	/**
	 * Returns all the terms for this committee
	 *
	 * @return Laminas\Db\ResultSet
	 */
	public function getTerms()
	{
		$terms = new TermTable();
		return $terms->find(['committee_id'=>$this->getId()]);
	}

	/**
	 * Returns all the people who have served on this committee
	 *
	 * @return Laminas\Db\ResultSet
	 */
	public function getMemberPeople()
	{
		$people = new PeopleTable();
		return $people->find(['committee_id'=>$this->getId()]);
	}

	/**
	 * Returns an array of Department objects with ID as the key
	 *
	 * @return array
	 */
	public function getDepartments()
	{
        if (!$this->departments) {
            $table = new DepartmentTable();
            $list = $table->find(['committee_id'=>$this->getId()]);
            foreach ($list as $d) {
                $this->departments[$d->getId()] = $d;
            }
        }
        return $this->departments;
	}

	/**
	 * @param Department $d
	 * @return boolean
	 */
	public function hasDepartment(Department $d)
	{
        return array_key_exists($d->getId(), $this->getDepartments());
	}

	/**
	 * @param array $ids An array of (int) department_ids
	 */
	public function setDepartments(array $ids=null)
	{
        if ($ids) {
            $current = array_keys($this->getDepartments());

            if (array_diff($current, $ids) || array_diff($ids, $current)) {
                $this->departments = [];
                $this->departmentsHaveChanged = true;

                foreach ($ids as $id) {
                    try { $this->departments[$id] = new Department($id); }
                    catch (\Exception $e) {
                        // Just ignore invalid departments for now
                    }
                }
            }
        }
        else {
            $this->departments = [];
            $this->departmentsHaveChanged = true;
        }
	}

	/**
	 * Application objects for this committee
	 *
	 * @param array $params Additional query parameters
	 * @return Laminas\Db\Result
	 */
	public function getApplications(array $params=null)
	{
        if (!$params) { $params = []; }
        $params['committee_id'] = $this->getId();

		$table = new ApplicationTable();
		return $table->find($params);
	}

	/**
	 * @return Laminas\Db\Result
	 */
	public function getStatutes()
	{
        $table = new CommitteeStatuteTable();
        return $table->find(['committee_id'=>$this->getId()]);
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	public static function data(array $fields=null)
	{
        $where = '';
        if (isset(   $fields['current'])) {
            $where = $fields['current']
                ? 'where (c.endDate is null     or  now() <= c.endDate)'
                : 'where (c.endDate is not null and now() >= c.endDate)';
        }

        $sql = "select  c.id, c.name, c.type, c.website, c.videoArchive, c.email, c.phone,
                        c.address, c.city, c.state, c.zip,
                        c.statutoryName, c.yearFormed, c.endDate, c.legislative,
                        count(s.id) as seats,
                        sum(
                            case when ((s.endDate is null or now() <= s.endDate) and s.type='termed' and t.id is not null and tm.id is null) then 1
                                 when ((s.endDate is null or now() <= s.endDate) and s.type='open'   and m.id is null)                       then 1
                                else 0
                            end
                        ) as vacancies,
                        (
                            select count(*)
                            from applications a
                            where a.committee_id=c.id and (a.archived is null or now() <= a.archived)
                        ) as applications
                from committees c
                left join seats s    on c.id= s.committee_id
                left join members m  on s.id= m.seat_id and  (m.startDate is null or  m.startDate <= now()) and ( m.endDate is null or now() <= m.endDate)
                left join terms   t  on s.id= t.seat_id and  (t.startDate is null or  t.startDate <= now()) and ( t.endDate is null or now() <= t.endDate)
                left join members tm on t.id=tm.term_id and (tm.startDate is null or tm.startDate <= now()) and (tm.endDate is null or now() <=tm.endDate)
                $where
                group by c.id
                order by c.name";
        $db = Database::getConnection();
        $result = $db->query($sql)->execute();
        return $result;
	}

	/**
	 * @return array  An array of meeting dates
	 */
	public function getMeetings(\DateTime $start, \DateTime $end)
	{
        $meetings = [];

        if ($this->getCalendarId()) {
            $events = GoogleGateway::events($this->getCalendarId(), $start, $end);
            foreach ($events as $e) {
                if ($e->start->dateTime) {
                    $allDay = false;
                    $eventStart = new \DateTime($e->start->dateTime);
                    $eventEnd   = new \DateTime($e->end  ->dateTime);
                }
                else {
                    $allDay = true;
                    $eventStart = new \DateTime($e->start->date);
                    $eventEnd   = new \DateTime($e->end  ->date);
                }
                $year  = $eventStart->format('Y');
                $month = $eventStart->format('m');
                $day   = $eventStart->format('d');

                $meetings[$eventStart->format('Y-m-d')] = [
                    'eventId'  => $e->id,
                    'summary'  => $e->summary,
                    'location' => $e->location,
                    'start'    => $eventStart->format('c'),
                    'end'      => $eventEnd  ->format('c')
                ];
            }
        }

        $table = new MeetingFilesTable();
        $list  = $table->find(['start'=>$start, 'end'=>$end, 'committee_id'=>$this->getId()]);
        foreach ($list as $f) {
            $meetings[$f->getMeetingDate('Y-m-d')]['files'][$f->getType()][] = $f;
        }
        ksort($meetings);

        return $meetings;
	}

	/**
	 * @return array
	 */
	public function getHistory()
	{
		$history = [];

		$db = Database::getConnection();
		$sql = 'select * from committeeHistory where committee_id=? order by date desc';
		$result = $db->query($sql)->execute([$this->getId()]);
		foreach ($result as $row) {
			$history[] = new CommitteeHistory($row);
		}
		return $history;
	}

	/**
	 * @return boolean
	 */
	public function isLegislative(): bool { return $this->getLegislative() ? true : false; }
	public function takesApplications(): bool
	{
        $db = Database::getConnection();
        $sql     = 'select count(*) as count from seats where takesApplications=1 and committee_id=?';
        $row     = $db->query($sql)->execute([$this->getId()])->current();
        return (int)$row['count'] > 0;
	}

	/**
	 * Returns an array suitable for serialization
	 *
	 * @return array
	 */
	public function toArray()
	{
        $c = [
            'id'               => (int)$this->getId(),
            'type'             => $this->getType(),
            'name'             => $this->getName(),
            'statutoryName'    => $this->getStatutoryName(),
            'website'          => $this->getWebsite(),
            'videoArchive'     => $this->getVideoArchive(),
            'email'            => $this->getEmail(),
            'phone'            => $this->getPhone(),
            'address'          => $this->getAddress(),
            'city'             => $this->getCity(),
            'state'            => $this->getState(),
            'zip'              => $this->getZip(),
            'calendarId'       => $this->getCalendarId(),
            'meetingSchedule'  => $this->getMeetingSchedule(),
            'vacancy'          => $this->hasVacancy(),
            'description'      => $this->getDescription(),
            'legislative'      => $this->isLegislative()
        ];
        $statutes = $this->getStatutes();
        if (count($statutes)) {
            $c['statutes'] = [];
            foreach ($statutes as $s) {
                $c['statutes'][] = ['citation'=>$s->getCitation(), 'url'=>$s->getUrl()];
            }
        }

        $c['liaisons'] = [];
        $data = LiaisonTable::committeeLiaisonData(['committee_id'=>$this->getId()]);
        foreach ($data['results'] as $row) {
            $liaison = [];
            foreach ($row as $f=>$v) {
                $liaison[$f] = (substr($f, -3) == '_id') ? (int)$v : $v;
            }

            $c['liaisons'][] = $liaison;
        }
        return $c;
	}
}
