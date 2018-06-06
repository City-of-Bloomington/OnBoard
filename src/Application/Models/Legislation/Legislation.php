<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
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
	protected $status;

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
	public function getId()           { return (int)parent::get('id'          ); }
	public function getNumber()       { return      parent::get('number'      ); }
	public function getTitle()        { return      parent::get('title'       ); }
	public function getSynopsis()     { return      parent::get('synopsis'    ); }
	public function getNotes()        { return      parent::get('notes'       ); }
	public function getCommittee_id() { return (int)parent::get('committee_id'); }
	public function getType_id()      { return (int)parent::get('type_id'     ); }
	public function getParent_id()    { return (int)parent::get('parent_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject('\Application\Models\Committee', 'committee_id'); }
	public function getType()         { return parent::getForeignKeyObject(__namespace__.'\Type',           'type_id'     ); }
	public function getParent()       { return parent::getForeignKeyObject(__namespace__.'\Legislation',    'parent_id'   ); }
	public function getStatus()       { return parent::getForeignKeyObject(__namespace__.'\Status',         'status_id'   ); }
	public function getAmendsCode()   { return parent::get('amendsCode') ? true : false; }
	public function getYear() {
        return !empty($this->data['year']) ? (int)$this->data['year'] : null;
    }
	public function getStatus_id()    {
        return !empty($this->data['status_id']) ? (int)$this->data['status_id'] : null;
    }

	public function setNumber   ($s) { parent::set('number',   $s); }
	public function setTitle    ($s) { parent::set('title',    $s); }
	public function setSynopsis ($s) { parent::set('synopsis', $s); }
	public function setNotes    ($s) { parent::set('notes',    $s); }
	public function setYear     ($s) { parent::set('year',     $s ? (int)$s : null); }
	public function setCommittee_id        ($i) { parent::setForeignKeyField ('\Application\Models\Committee', 'committee_id', $i); }
	public function setCommittee (Committee $o) { parent::setForeignKeyObject('\Application\Models\Committee', 'committee_id', $o); }
	public function setType_id             ($i) { parent::setForeignKeyField (__namespace__.'\Type',        'type_id',   $i); }
	public function setType      (Type      $o) { parent::setForeignKeyObject(__namespace__.'\Type',        'type_id',   $o); }
	public function setParent_id           ($i) { parent::setForeignKeyField (__namespace__.'\Legislation', 'parent_id', $i); }
	public function setParent  (Legislation $o) { parent::setForeignKeyObject(__namespace__.'\Legislation', 'parent_id', $o); }
	public function setStatus_id           ($i) { parent::setForeignKeyField (__namespace__.'\Status',      'status_id', $i); }
	public function setStatus       (Status $o) { parent::setForeignKeyObject(__namespace__.'\Status',      'status_id', $o); }
	public function setAmendsCode($b) { $this->data['amendsCode'] = $b ? 1 : 0; }

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
        $fields = [
            'number', 'title', 'synopsis', 'notes', 'year',
            'committee_id', 'type_id', 'status_id', 'amendsCode'
        ];
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
	 * @return array  An array of Action objects
	 */
	public function getActions(array $fields=null)
	{
        $search = $fields ? $fields : [];
        $search['legislation_id'] = $this->getId();

        $table = new ActionsTable();
        return $table->find($search);
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

	/**
	 * Returns a data structure ready for serialization
	 *
	 * @return array
	 */
    public function toArray()
    {
        $actions = [];
        foreach ($this->getActions() as $a) {
            $actions[] = [
                'name'    => $a->getType()->getName(),
                'date'    => $a->getActionDate(),
                'outcome' => $a->getOutcome(),
                'vote'    => $a->getVote()
            ];
        }

        $files = [];
        foreach ($this->getFiles() as $f) {
            $files[] = [
                'url' => BASE_URL.'/legislationFiles/download?id='.$f->getId()
            ];
        }

        $status = $this->getStatus_id() ? $this->getStatus()->getName() : '';

        return [
            'id'         => $this->getId(),
            'committee'  => $this->getCommittee()->getName(),
            'type'       => $this->getType()->getName(),
            'number'     => $this->getNumber(),
            'year'       => $this->getYear(),
            'status'     => $status,
            'amendsCode' => $this->amendsCode(),
            'title'      => $this->getTitle(),
            'synopsis'   => $this->getSynopsis(),
            'notes'      => $this->getNotes(),
            'actions'    => $actions,
            'files'      => $files
        ];
    }
}
