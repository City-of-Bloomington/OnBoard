<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\ActiveRecord;
use Application\Database;

class Status extends ActiveRecord
{
    public const TABLENAME = 'legislationStatuses';

	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
                $sql = ActiveRecord::isId($id)
                    ? 'select * from legislationStatuses where id=?'
                    : 'select * from legislationStatuses where name=?';
				$result = Database::query($sql, [$id]);
				if (count($result)) {
					$this->exchangeArray($result[0]);
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

	/**
	 * @throws \Exception
	 */
	public function validate()
	{
        if (!$this->getName()) { throw new \Exception('missingRequiredFields'); }
	}

	public function save() { parent::save(); }

	public function delete()
	{
        $sql = 'update legislation set status_id=null where status_id=?';
		Database::execute($sql, [$this->getId()]);
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

	public function __toString() { return parent::get('name'); }
}
