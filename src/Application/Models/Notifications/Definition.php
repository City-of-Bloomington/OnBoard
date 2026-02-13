<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Web\Database;

class Definition extends \Web\ActiveRecord
{
    protected $tablename = 'notification_definitions';
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
     * @param int|string|array $id
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $db  = Database::getConnection();
                $sql = 'select * from notification_definitions where id=?';
                $res = $db->createStatement($sql)->execute([$id]);
                if (count($res)) {
                    $this->exchangeArray($res->current());
                }
                else {
                    throw new \Exception('notifications/unknownDefinition');
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
     *
     * @throws \Exception $e
     */
    public function validate()
    {
        if (!$this->getEvent() || !$this->getSubject() || !$this->getBody()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!in_array($this->getEvent(), array_keys(DefinitionTable::$events))) {
            throw new \Exception('notifications/invalidEvent');
        }
    }

    public function save() { parent::save(); }
    public function delete() { parent::delete(); }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()           { return parent::get('id'); }
    public function getEvent()        { return parent::get('event'); }
    public function getSubject()      { return parent::get('subject'); }
    public function getBody()         { return parent::get('body'); }
    public function getCommittee_id() { return parent::get('committee_id'); }
    public function getCommittee()    { return parent::getForeignKeyObject(self::COMMITTEE, 'committee_id'); }

    public function setEvent   ($s) { parent::set('event',    $s); }
    public function setSubject ($s) { parent::set('subject',  $s); }
    public function setBody    ($s) { parent::set('body',     $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField (self::COMMITTEE, 'committee_id', $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject(self::COMMITTEE, 'committee_id', $o); }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function handleUpdate(array $post)
    {
        $this->setEvent       ($post['event'       ]);
        $this->setCommittee_id($post['committee_id']);
        $this->setSubject     ($post['subject'     ]);
        $this->setBody        ($post['body'        ]);
    }

    /**
     * @return array    An array of people objects
     */
    public function getSubscribers(int $committee_id): array
    {
        $o = [];
        $t = new SubscriptionTable();
        $l = $t->find(['committee_id'=>$committee_id, 'event'=>$this->getEvent()]);
        foreach ($l['rows'] as $s) { $o[] = $s->getPerson(); }
        return $o;
    }

    /**
     * An array of people objects
     *
     * @param array  $people  Non-subscribers to include in the notification
     * @param object $model   Object to use for template variables
     */
    public function send(array $people, Model $model)
    {
        $s = new \Web\Notifications\View($this->getSubject(), $model);
        $b = new \Web\Notifications\View($this->getBody(),    $model);
        $subject = $s->render();
        $body    = $b->render();
        $subs    = $this->getSubscribers($model->getCommittee_id());
        $rec     = array_merge($subs, $people);

        foreach ($rec as $p) {
            $to = $p->getEmail();
            if ($to) {
                $mail = new Email();
                $mail->setSubject($subject);
                $mail->setBody($body);
                $mail->setEmailFrom('no-reply@'.BASE_HOST, APPLICATION_NAME);
                $mail->setEmailTo($to);
                $mail->setEvent($this->getEvent());
                $mail->setCommittee_id($model->getCommittee_id());
                $mail->save();
            }
        }
    }
}
