<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Web\ActiveRecord;
use Web\Database;
use Laminas\Db\Sql\Sql;

class Email extends ActiveRecord
{
	protected $tablename = 'people_emails';
	protected $person;

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
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
                $this->exchangeArray($id);
			}
			else {
				$db = Database::getConnection();
				$sql = 'select * from people_emails where id=?';
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('emails/unknown');
                }
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

    /**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     *
     * @Override
     * @param array $data
     */
    public function exchangeArray($data)
    {
        parent::exchangeArray($data);

        $this->person = null;
    }

	public function validate()
	{
        if (!self::isValidFormat($this->getEmail())) { throw new \Exception('email/invalidFormat'); }

		if (!$this->getPerson_id()) { throw new \Exception('missingRequiredFields'); }

		// Make sure there's a main email address
		$t = new EmailTable();
        $l = $t->find(['person_id'=>$this->getPerson_id(), 'main'=>1]);
        if (!count($l)) { $this->setMain(true); }

        // Make sure there's only one main
        if ($this->getMain()) { $this->saveMain(); }
	}

	private function saveMain()
    {
        $db  = Database::getConnection();
        if ($this->getId()) {
            $sql = 'update people_emails set main=null where person_id=? and id!=?';
            $db->query($sql)->execute([$this->getPerson_id(), $this->getId()]);
        }
        else {
            $sql = 'update people_emails set main=null where person_id=?';
            $db->query($sql)->execute([$this->getPerson_id()]);
        }
    }

	public function save()   { parent::save();   }
	public function delete()
	{
		$person = $this->getPerson();
        $main   = $this->getMain();

		parent::delete();

        if ($main) {
            $l = $person->getEmails();
            if ($l) {
                $l[0]->setMain(true);
                $l[0]->save();
            }
        }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()    { return parent::get('id'   ); }
	public function getEmail() { return parent::get('email'); }
	public function getMain()  { return parent::get('main' ) ? true : false; }

	public function setEmail($s) { parent::set('email', $s); }
	public function setMain ($b) { $this->data['main'] = $b ? 1 : null; }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $p);  }

	public function handleUpdate($post)
	{
        $fields = ['email', 'person_id', 'main'];
		foreach ($fields as $f) {
			if (isset($post[$f])) {
				$set = 'set'.ucfirst($f);
				$this->$set($post[$f]);
			}
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString(): string { return "{$this->getEmail()}"; }

    public static function isValidFormat(string $email): bool
    {
        $regex = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";
        return preg_match($regex, $email);
    }
}
