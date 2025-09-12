<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Web\Database;
use Application\Models\Notifications\DefinitionTable;

class Subscription extends \Web\ActiveRecord
{
    protected $tablename = 'notification_subscriptions';

    protected $person;
    protected $committee;

    private const PERSON    = 'Application\Models\Person';
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
                $sql = 'select * from notification_subscriptions where id=?';
                $res = $db->createStatement($sql)->execute([$id]);
                if (count($res)) {
                    $this->exchangeArray($res->current());
                }
                else {
                    throw new \Exception('noitifications/unknownSubscription');
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
        if (!$this->getPerson_id() || !$this->getEvent() || !$this->getCommittee_id()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!in_array($this->getEvent(), array_keys(DefinitionTable::$events))) {
            throw new \Exception('notifications/invalidEvent');
        }
    }

    public function save()   { parent::save(); }
    public function delete() { parent::delete(); }

    public function getId()            { return parent::get('id'); }
    public function getEvent()         { return parent::get('event'); }
    public function getPerson_id()     { return parent::get('person_id'); }
    public function getCommittee_id()  { return parent::get('committee_id'); }
    public function getPerson()        { return parent::getForeignKeyObject(self::PERSON,    'person_id'); }
    public function getCommittee()     { return parent::getForeignKeyObject(self::COMMITTEE, 'committee_id'); }

    public function setEvent($s) { parent::set('event', $s); }
    public function setPerson_id   ($i) { parent::setForeignKeyField(self::PERSON,     'person_id',     $i); }
    public function setCommittee_id($i) { parent::setForeignKeyField(self::COMMITTEE,  'committee_id',  $i); }
    public function setPerson   ($o) { parent::setForeignKeyObject(self::PERSON,     'person_id',     $o); }
    public function setCommittee($o) { parent::setForeignKeyObject(self::COMMITTEE,  'committee_id',  $o); }

    public function handleUpdate(array $post)
    {
        $this->setPerson_id   ($post['person_id'   ]);
        $this->setCommittee_id($post['committee_id']);
        $this->setEvent       ($post['event'       ]);
    }
}
