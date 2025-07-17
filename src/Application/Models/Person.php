<?php
/**
 * @copyright 2009-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Auth\ExternalIdentity;
use Web\Database;
use Web\View;
use PHPMailer\PHPMailer\PHPMailer;

class Person extends ActiveRecord
{
    protected $tablename = 'people';
    protected $race;
    protected $department;

    public static $STATES = ['IN'];

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
            if (is_array($id) || $id instanceof ArrayObject) {
                $this->exchangeArray($id);
            }
            else {
                $db = Database::getConnection();
                if (ActiveRecord::isId($id)) {
                    $sql = 'select * from people where id=?';
                }
                else {
                    $sql = 'select * from people where username=?';
                }
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('people/unknownPerson');
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
        // Check for required fields here.  Throw an exception if anything is missing.
        if (!$this->getFirstname() || !$this->getLastname()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save() { parent::save(); }
    public function delete()
    {
        if ($this->isSafeToDelete()) {
            $db = Database::getConnection();
            $db->query('delete from people_phones where person_id=?', [$this->getId()]);
            $db->query('delete from people_emails where person_id=?', [$this->getId()]);

            parent::delete();
        }
    }

    /**
     * Removes all the user account related fields from this Person
     */
    public function deleteUserAccount()
    {
        $this->data['username'] = null;
        $this->data['role'    ] = null;
    }

    public function getId():int     { return (int)parent::get('id');   }
    public function getFirstname()  { return parent::get('firstname'); }
    public function getLastname()   { return parent::get('lastname');  }
    public function getAddress()    { return parent::get('address');   }
    public function getCity()       { return parent::get('city');      }
    public function getState()      { return parent::get('state');     }
    public function getZip()        { return parent::get('zip');       }
    public function getOccupation() { return parent::get('occupation'); }
    public function getWebsite()    { return parent::get('website');   }
    public function getCitylimits(): bool { return parent::get('citylimits') ? true : false; }
    public function getCreated($f=null)   { return parent::getDateData('created', $f); }
    public function getUpdated($f=null)   { return parent::getDateData('updated', $f); }

    public function setFirstname ($s) { parent::set('firstname', $s); }
    public function setLastname  ($s) { parent::set('lastname',  $s); }
    public function setAddress   ($s) { parent::set('address',   $s); }
    public function setCity      ($s) { parent::set('city',      $s); }
    public function setState     ($s) { parent::set('state',     $s); }
    public function setZip       ($s) { parent::set('zip',       $s); }
    public function setWebsite   ($s) { parent::set('website',   $s); }
    public function setOccupation($s) { parent::set('occupation', $s); }
    public function setCitylimits($s) { $this->data['citylimits'] = $s ? 1 : 0; }

    public function getUsername() { return parent::get('username'); }
    public function getRole()     { return parent::get('role');     }
    public function getDepartment_id(): ?int     { return !empty($this->data['department_id']) ? (int)$this->data['department_id'] : null; }
    public function getDepartment(): ?Department { return parent::getForeignKeyObject(__namespace__.'\Department', 'department_id'); }

    public function setUsername($s) { parent::set('username', $s); }
    public function setRole    ($s) { parent::set('role',     $s); }
    public function setDepartment_id        ($i) { parent::setForeignKeyField (__namespace__.'\Department', 'department_id', $i); }
    public function setDepartment(Department $o) { parent::setForeignKeyObject(__namespace__.'\Department', 'department_id', $o); }

    public function handleUpdate(array $post)
    {
        $fields = [
            'firstname', 'middlename', 'lastname',
            'address', 'city', 'state', 'zip', 'website',
            'citylimits', 'occupation'
        ];
        foreach ($fields as $field) {
            if (isset($post[$field])) {
                $set = 'set'.ucfirst($field);
                $this->$set($post[$field]);
            }
        }
    }

    public function handleUpdateUserAccount(array $post)
    {
        $fields = ['username', 'role', 'department_id'];
        foreach ($fields as $f) {
            if (isset($post[$f])) {
                $set = 'set'.ucfirst($f);
                $this->$set($post[$f]);
            }
        }
    }

    /**
     * Checks if the user is supposed to have acces to the resource
     *
     * This is implemented by checking against a Laminas ACL object
     * The ACL should be created in bootstrap.php
     *
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public static function isAllowed($resource, $action=null)
    {
        global $ACL;

        $role = 'Anonymous';
        if (isset(  $_SESSION['USER']) && $_SESSION['USER']->getRole()) {
            $role = $_SESSION['USER']->getRole();
        }
        return $ACL->isAllowed($role, $resource, $action);
    }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    /**
     * @return string
     */
    public function getFullname()
    {
        return "{$this->getFirstname()} {$this->getLastname()}";
    }

    /**
     * Returns this person's main email address
     */
    public function getEmail(): ?Email
    {
        $t = new EmailTable();
        $l = $t->find(['person_id'=>$this->getId(), 'main'=>1]);
        if (count($l)) { return $l->current(); }
        return null;
    }

    /**
     * Returns all of this person's email records
     *
     * @return array   An array of Email objects
     */
    public function getEmails(): array
    {
        $o = [];
        $t = new EmailTable();
        $l = $t->find(['person_id'=>$this->getId()]);
        foreach ($l as $e) { $o[] = $e; }
        return $o;
    }

    public function hasEmail(string $email): bool
    {
        if ($this->getId()) {
            $t = new EmailTable();
            $l = $t->find(['person_id'=>$this->getId(), 'email'=>$email]);
            return count($l) ? true : false;
        }
        return false;
    }

    public function saveEmail(string $email)
    {
        if (!$this->hasEmail($email)) {
            $e = new Email();
            $e->setPerson($this);
            $e->setEmail($email);
            $e->save();
        }
    }

    public function getPhone(): ?Phone
    {
        $t = new PhoneTable();
        $l = $t->find(['person_id'=>$this->getId(), 'main'=>1]);
        if (count($l)) { return $l->current(); }
        return null;
    }

    public function getPhones(): array
    {
        $o = [];
        $t = new PhoneTable();
        $l = $t->find(['person_id'=>$this->getId()]);
        foreach ($l as $p) { $o[] = $p; }
        return $o;
    }

    public function hasPhone(string $number): bool
    {
        if ($this->getId()) {
            $t = new PhoneTable();
            $l = $t->find(['person_id'=>$this->getId(), 'number'=>$number]);
            return count($l) ? true : false;
        }
        return false;
    }

    public function savePhone(string $number)
    {
        if (!$this->hasPhone($number)) {
            $p = new Phone();
            $p->setPerson($this);
            $p->setNumber($number);
            $p->save();
        }
    }

    /**
     * @return Laminas\Db\ResultSet
     */
    public function getMemberCommittees()
    {
        $table = new CommitteeTable();
        return $table->find(['member_id'=>$this->getId()]);
    }

    /**
     * @return array An array of Committee objects
     */
    public function getLiaisonCommittees()
    {
        $sql = 'select distinct c.*
                from liaisons l
                join committees c on l.committee_id=c.id
                where l.person_id=?';
        $db = Database::getConnection();
        $result = $db->query($sql, [$this->getId()]);

        $committees = [];
        foreach ($result->toArray() as $row) {
            $committees[] = new Committee($row);
        }
        return $committees;
    }

    /**
     * @param array $fields Extra fields to search on
     * @return Laminas\Db\ResultSet
     */
    public function getMembers($fields=null)
    {
        $fields['person_id'] = $this->getId();

        $table = new MemberTable();
        return $table->find($fields);
    }

    /**
     * @return array An array of People in the same committes
     */
    public function getPeers()
    {
        $peers = array();

        $committees = array();
        foreach ($this->getMemberCommittees() as $committee) {
            $committees[] = $committee->getId();
        }
        if (count($committees)) {
            $table = new PeopleTable();
            $list = $table->find(['committee_id'=>$committees]);
            foreach ($list as $person) {
                if ($person->getId() != $this->getId()) {
                    $peers[] = $person;
                }
            }
        }
        return $peers;
    }

    /**
     * Returns the offices held for the given committee
     *
     * If a date is given, it will return only offices held on that date
     *
     * @param Committee $committee
     * @param string $date
     * @return array An array of Office objects
     */
    public function getOffices(Committee $committee=null, $date=null)
    {
        $search = ['person_id'=>$this->getId()];
        if ($committee) {
            $search['committee_id'] = $committee->getId();
        }
        if ($date) {
            $search['current'] = $date;
        }


        $offices = [];
        $table = new OfficeTable();
        foreach ($table->find($search) as $o) {
            $offices[] = $o;
        }
        return $offices;
    }

    /**
     * Returns all the appointment information for a person.
     *
     * Optionally provide a committee to limit the appointment information
     *
     * @param Committee $committee
     * @return Laminas\Db\ResultSet
     */
    public function getAppointers(Committee $committee=null)
    {
        $search = ['person_id'=>$this->getId()];
        if ($committee) {
            $search['committee_id'] = $committee->getId();
        }
        $table = new AppointerTable();
        return $table->find($search);
    }

    /**
     * @return boolean
     */
    public function isSafeToDelete()
    {
        $id = (int)$this->getId();

        $sql = "select id from members  where person_id=$id
          union select id from liaisons where person_id=$id
          union select id from offices  where person_id=$id";
        $db = Database::getConnection();
        $result  = $db->query($sql)->execute();
        return count($result) ? false : true;
    }

    public function isInvolved(): bool
    {
        $sql = "select id from members where person_id=?
                union
                select id from alternates where person_id=?
                union
                select id from liaisons where person_id=?
                union
                select id from offices where person_id=?";
        $db = Database::getConnection();
        $id = $this->getId();
        $r  = $db->query($sql, [$id, $id, $id, $id]);
        return count($r) ? true : false;
    }

    public function sendNotification(string $message, string $subject=null, string $replyTo=null)
    {
        $to = $this->getEmail();
        if ($to) {
            if (!$subject) {
                $subject = APPLICATION_NAME.' Notification';
            }

            $mail = new PHPMailer(true);
            $mail->isHTML(false);
            $mail->isSMTP();
            $mail->Host        = SMTP_HOST;
            $mail->Port        = SMTP_PORT;
            $mail->SMTPSecure  = false;
            $mail->SMTPAutoTLS = false;
            $mail->Subject     = $subject;
            $mail->Body        = $message;
            $mail->setFrom('no-reply@'.BASE_HOST, APPLICATION_NAME);
            $mail->addAddress($to);
            $mail->send();
        }
    }

    /**
     * Applications for this applicant
     *
     * @param array $params Additional query parameters
     */
    public function getApplications(array $params=null): array
    {
        $out = [];
        if ($this->getId()) {
            if (!$params) { $params = []; }
            $params['person_id'] = $this->getId();

            $t = new ApplicationTable();
            $l = $t->find($params);
            foreach ($l as $a) { $out[] = $a; }
        }
        return $out;
    }

    /**
     * @return array An array of File objects
     */
    public function getFiles(): array
    {
        $files = [];
        if ($this->getId()) {
            $t = new ApplicantFilesTable();
            $l  = $t->find(['person_id'=>$this->getId()]);
            foreach ($l as $f) { $files[] = $f; }
        }
        return $files;
    }

}
