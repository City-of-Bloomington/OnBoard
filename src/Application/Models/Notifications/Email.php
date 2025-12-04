<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Web\ActiveRecord;
use Web\Database;
use PHPMailer\PHPMailer\PHPMailer;

use Application\Models\Committee;

class Email extends ActiveRecord
{
    protected $tablename = 'email_queue';
    protected $committee;
    private const COMMITTEE = 'Application\Models\Committee';

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
            if (is_array($id) || $id instanceof \ArrayObject) {
                $this->exchangeArray($id);
            }
            else {
                $db  = Database::getConnection();
                $sql = 'select * from email_queue where id=?';
                $res = $db->createStatement($sql)->execute([$id]);
                if (count($res)) {
                    $this->exchangeArray($res->current());
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
        if (!$this->getEmailfrom() || !$this->getEmailto() || !$this->getSubject()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save() { parent::save(); }

    public function getId(): int    { return (int)parent::get('id');   }
    public function getEmailfrom()  { return parent::get('emailfrom'); }
    public function getEmailto()    { return parent::get('emailto'  ); }
    public function getCc()         { return parent::get('cc'       ); }
    public function getBcc()        { return parent::get('bcc'      ); }
    public function getSubject()    { return parent::get('subject'  ); }
    public function getBody()       { return parent::get('body'     ); }
    public function getEvent()      { return parent::get('event'    ); }
    public function getCommittee_id(): int { return parent::get('committee_id'); }
    public function getCommittee(): Committee { return parent::getForeignKeyObject(self::COMMITTEE, 'committee_id'); }

    public function getCreated(?string $format=null) { return parent::getDateData('created', $format); }
    public function getSent   (?string $format=null) { return parent::getDateData('sent',    $format); }

    public function setEmailfrom($s) { parent::set('emailfrom', $s); }
    public function setEmailto  ($s) { parent::set('emailto',   $s); }
    public function setCc       ($s) { parent::set('cc',        $s); }
    public function setBcc      ($s) { parent::set('bcc',       $s); }
    public function setSubject  ($s) { parent::set('subject',   $s); }
    public function setBody     ($s) { parent::set('body',      $s); }
    public function setEvent    ($s) { parent::set('event',    $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField (self::COMMITTEE, 'committee_id', $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject(self::COMMITTEE, 'committee_id', $o); }

    public function send()
    {
        if (defined('SMTP_HOST') && defined('SMTP_PORT')) {
            $to   = $this->getEmailto()   ? explode(';', $this->getEmailto()  ) : null;
            $cc   = $this->getCc()        ? explode(';', $this->getCc()       ) : null;
            $bcc  = $this->getBcc()       ? explode(';', $this->getBcc()      ) : null;

            $mail = new PHPMailer(true);
            $mail->isHTML(false);
            $mail->isSMTP();
            $mail->Host        = SMTP_HOST;
            $mail->Port        = SMTP_PORT;
            $mail->SMTPSecure  = false;
            $mail->SMTPAutoTLS = false;
            $mail->Subject     = $this->getSubject();
            $mail->Body        = $this->getBody();
            $mail->setFrom($this->getEmailFrom());

            if ($to  ) { foreach ($to   as $a) { $mail->addAddress($a); }}
            if ($cc  ) { foreach ($cc   as $c) { $mail->addCC($c);      }}
            if ($bcc ) { foreach ($bcc  as $c) { $mail->addBCC($c);     }}

            $mail->send();

            $db  = Database::getConnection();
            $sql = 'update email_queue set sent=now() where id=?';
            $db->query($sql)->execute([$this->getId()]);
        }
    }
}
