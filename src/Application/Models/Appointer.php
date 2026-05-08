<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\ActiveRecord;
use Application\Database;

class Appointer extends ActiveRecord
{
    public const TABLENAME = 'appointers';

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
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $sql = ActiveRecord::isId($id)
                     ? 'select * from appointers where id=?'
                     : 'select * from appointers where name=?';
                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
                }
                else {
                    throw new \Exception('apopinters/unknownAppointer');
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
     * @throws \Exception
     */
    public function validate()
    {
        if (!$this->getName()) { throw new \Exception('missingName'); }
    }

    public function save() { parent::save(); }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()   { return parent::get('id');   }
    public function getName() { return parent::get('name'); }

    public function setName($s) { parent::set('name', $s); }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function __toString() { return parent::get('name'); }
}
