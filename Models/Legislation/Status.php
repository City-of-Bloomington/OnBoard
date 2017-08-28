<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

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
				$zend_db = Database::getConnection();
                $sql = ActiveRecord::isId($id)
                    ? 'select * from legislationStatuses where id=?'
                    : 'select * from legislationStatuses where name=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
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

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()      { return parent::get('id'  ); }
	public function getName()    { return parent::get('name'); }

	public function setName   ($s) { parent::set('name',    $s); }

	public function handleUpdate(array $post)
	{
        $this->setName($post['name']);
	}
}
