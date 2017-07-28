<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\Models\Committee;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Legislation extends ActiveRecord
{
	protected $tablename = 'legislation';
	protected $committee;
	protected $type;

	private $actions = [];

	public static function actionTypes()
	{
        $table = new ActionTypesTable();
        return $table->find();
	}

	/**
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from legislation where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('legislation/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
        if (!$this->getCommittee_id() || !$this->getType_id() || !$this->getTitle() || !$this->getNumber()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return (int)parent::get('id'          ); }
	public function getNumber()       { return      parent::get('number'      ); }
	public function getTitle()        { return      parent::get('title'       ); }
	public function getSynopsis()     { return      parent::get('synopsis'    ); }
	public function getCommittee_id() { return (int)parent::get('committee_id'); }
	public function getType_id()      { return (int)parent::get('type_id'     ); }
	public function getCommittee()    { return parent::getForeignKeyObject('\Application\Models\Committee', 'committee_id'); }
	public function getType()         { return parent::getForeignKeyObject(__namespace__.'\Type',            'type_id'     ); }

	public function setNumber  ($s) { parent::set('number',   $s); }
	public function setTitle   ($s) { parent::set('title',    $s); }
	public function setSynopsis($s) { parent::set('synopsis', $s); }
	public function setCommittee_id(int       $i) { parent::setForeignKeyField ('\Application\Models\Committee', 'committee_id', $i); }
	public function setCommittee   (Committee $o) { parent::setForeignKeyObject('\Application\Models\Committee', 'committee_id', $o); }
	public function setType_id     (int       $i) { parent::setForeignKeyField (__namespace__.'\Type', 'type_id', $i); }
	public function setType        (Type      $o) { parent::setForeignKeyObject(__namespace__.'\Type', 'type_id', $o); }

	public function handleUpdate(array $post)
	{
        $this->setNumber      (     $post['number'      ]);
        $this->setTitle       (     $post['title'       ]);
        $this->setSynopsis    (     $post['synopsis'    ]);
        $this->setCommittee_id((int)$post['committee_id']);
        $this->setType_id     ((int)$post['type_id'     ]);
	}

	//----------------------------------------------------------------
	// Custom functions
	//----------------------------------------------------------------
	/**
	 * Returns an array of Actions
	 *
	 * The array uses the ActionType as the key.
	 * There should only be one action for each ActionType.
	 *
	 * @return array  An array of Action objects
	 */
	public function getActions()
	{
        if (!$this->actions) {
            $table = new ActionsTable();
            $list = $table->find(['legislation_id'=>$this->getId()]);
            foreach ($list as $a) {
                $type = $a->getType()->getName();
                $this->actions[$type] = $a;
            }
        }
        return $this->actions;
	}


	/**
	 * @return Action
	 */
	public function getAction(ActionType $type)
	{
        $actions = $this->getActions();
        if (isset ($actions[$type->getName()])) {
            return $actions[$type->getName()];
        }
	}
}
