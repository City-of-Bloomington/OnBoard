<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\ActiveRecord;
use Web\Database;

class Status extends ActiveRecord
{
    protected $tablename = 'legislationStatuses';

	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$db = Database::getConnection();
                $sql = ActiveRecord::isId($id)
                    ? 'select * from legislationStatuses where id=?'
                    : 'select * from legislationStatuses where name=?';

				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('legislationActions/unknown');
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
        if (!$this->getName()) { throw new \Exception('missingRequiredFields'); }
	}

	public function save() { parent::save(); }

	public function delete()
	{
        $db = Database::getConnection();
        $sql = 'update legislation set status_id=null where status_id=?';
        $db->createStatement($sql)->execute([$this->getId()]);
        parent::delete();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()      { return parent::get('id'  ); }
	public function getName()    { return parent::get('name'); }
	public function getActive()  { return parent::get('active') ? 1 : 0; }

	public function setName   ($s) { parent::set('name',   $s); }
	public function setActive ($i) { $this->data['active'] = $i ? 1 : 0; }

	public function handleUpdate(array $post)
	{
        $this->setName  ($post['name'  ]);
        $this->setActive($post['active'] ?? false);
	}
}
