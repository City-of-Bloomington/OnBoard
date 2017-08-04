<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Application\Models\Committee;
use Application\Models\TagsTable;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Legislation extends ActiveRecord
{
	protected $tablename = 'legislation';
	protected $committee;
	protected $type;
	protected $parent;

	private $actions = [];
	private $tags    = [];

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
			$this->setYear((int)date('Y'));
		}
	}

	public function validate()
	{
        if (!$this->getCommittee_id() || !$this->getType_id() || !$this->getTitle() || !$this->getNumber()) {
            throw new \Exception('missingRequiredFields');
        }

        if ($this->getParent_id()) {
            if (!$this->getType()->isSubtype()) {
                throw new \Exception('legislation/invalidType');
            }
        }

        if ($this->getType()->isSubtype()) {
            if (!$this->getParent_id()) {
                throw new \Exception('legislation/missingParent');
            }
        }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getNumber()       { return parent::get('number'      ); }
	public function getTitle()        { return parent::get('title'       ); }
	public function getSynopsis()     { return parent::get('synopsis'    ); }
	public function getYear()         { return parent::get('year'        ); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getType_id()      { return parent::get('type_id'     ); }
	public function getParent_id()    { return parent::get('parent_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject('\Application\Models\Committee', 'committee_id'); }
	public function getType()         { return parent::getForeignKeyObject(__namespace__.'\Type',           'type_id'     ); }
	public function getParent()       { return parent::getForeignKeyObject(__namespace__.'\Legislation',    'parent_id'   ); }
	public function getAmendsCode() { return parent::get('amendsCode') ? true : false; }

	public function setNumber   ($s) { parent::set('number',   $s); }
	public function setTitle    ($s) { parent::set('title',    $s); }
	public function setSynopsis ($s) { parent::set('synopsis', $s); }
	public function setYear (int $s) { parent::set('year',     $s); }
	public function setCommittee_id        ($i) { parent::setForeignKeyField ('\Application\Models\Committee', 'committee_id', $i); }
	public function setCommittee (Committee $o) { parent::setForeignKeyObject('\Application\Models\Committee', 'committee_id', $o); }
	public function setType_id             ($i) { parent::setForeignKeyField (__namespace__.'\Type', 'type_id', $i); }
	public function setType      (Type      $o) { parent::setForeignKeyObject(__namespace__.'\Type', 'type_id', $o); }
	public function setParent_id           ($i) { parent::setForeignKeyField (__namespace__.'\Legislation', 'parent_id', $i); }
	public function setParent  (Legislation $o) { parent::setForeignKeyObject(__namespace__.'\Legislation', 'parent_id', $o); }
	public function setAmendsCode($b) { parent::set('amendsCode', $b ? 1 : 0); }

	/**
	 * Handler for Controller::update action
	 *
	 * This function calls save() automatically.  There is no
	 * need to call save() after calling this function.
	 *
	 * @param array $post
	 */
	public function handleUpdate(array $post)
	{
        $fields = ['number', 'title', 'synopsis', 'year', 'committee_id', 'type_id', 'amendsCode'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }

        $this->save();

        isset($post['tags'])
            ? $this->saveTags(array_keys($post['tags']))
            : $this->saveTags([]);
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

	/**
	 * @return array  An array of LegislationFile objects
	 */
	public function getFiles()
	{
        $table = new LegislationFilesTable();
        return $table->find(['legislation_id'=>$this->getId()]);
	}

	/**
	 * @return array  An array of Tag objects, indexed by ID
	 */
	public function getTags()
	{
        if (!$this->tags) {
            $table = new TagsTable();
            $list  = $table->find(['legislation_id'=>$this->getId()]);
            foreach ($list as $t) { $this->tags[$t->getId()] = $t; }
        }
        return $this->tags;
	}

	/**
	 * Saves a set of tags directly to the database
	 *
	 * @param array $tag_ids  An array of ID numbers for the tags
	 */
	public function saveTags(array $tag_ids)
	{
        $id = $this->getId();
        if ($id) {
            $zend_db = Database::getConnection();

            $sql = 'delete from legislation_tags where legislation_id=?';
            $zend_db->query($sql)->execute([$id]);

            $sql = 'insert into legislation_tags (legislation_id, tag_id) values(?, ?)';
            $insert = $zend_db->createStatement($sql);
            foreach ($tag_ids as $tid) {
                $insert->execute([$id, $tid]);
            }
        }
	}

	public function getChildren()
	{
        $table = new LegislationTable();
        return $table->find(['parent_id'=>$this->getId()]);
	}

	public function amendsCode() { return $this->getAmendsCode(); }
}
