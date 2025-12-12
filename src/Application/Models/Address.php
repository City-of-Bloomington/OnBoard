<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class Address extends ActiveRecord
{
    protected $tablename = 'people_addresses';
    protected $person;

    public static $STATES = ['AL' => 'Alabama',
							 'AK' => 'Alaska',
							 'AZ' => 'Arizona',
							 'AR' => 'Arkansas',
							 'CA' => 'California',
							 'CO' => 'Colorado',
							 'CT' => 'Connecticut',
							 'DE' => 'Delaware',
							 'DC' => 'District of Columbia',
							 'FL' => 'Florida',
							 'GA' => 'Georgia',
							 'HI' => 'Hawaii',
							 'ID' => 'Idaho',
							 'IL' => 'Illinois',
							 'IN' => 'Indiana',
							 'IA' => 'Iowa',
							 'KS' => 'Kansas',
							 'KY' => 'Kentucky',
							 'LA' => 'Louisiana',
							 'ME' => 'Maine',
							 'MD' => 'Maryland',
							 'MA' => 'Massachusetts',
							 'MI' => 'Michigan',
							 'MN' => 'Minnesota',
							 'MS' => 'Mississippi',
							 'MO' => 'Missouri',
							 'MT' => 'Montana',
							 'NB' => 'Nebraska',
							 'NV' => 'Nevada',
							 'NH' => 'New Hampshire',
							 'NJ' => 'New Jersey',
							 'NM' => 'New Mexico',
							 'NY' => 'New York',
							 'NC' => 'North Carolina',
							 'ND' => 'North Dakota',
							 'OH' => 'Ohio',
							 'OK' => 'Oklahoma',
							 'OR' => 'Oregon',
							 'PA' => 'Pennsylvania',
							 'PR' => 'Puerto Rico',
							 'RI' => 'Rhode Island',
							 'SC' => 'South Carolina',
							 'SD' => 'South Dakota',
							 'TN' => 'Tennessee',
							 'TX' => 'Texas',
							 'UT' => 'Utah',
							 'VT' => 'Vermont',
							 'VA' => 'Virginia',
							 'WA' => 'Washington',
							 'WV' => 'West Virginia',
							 'WI' => 'Wisconsin',
							 'WY' => 'Wyoming'];

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
			if (is_array($id)) { $this->exchangeArray($id); }
			else {
				$db     = Database::getConnection();
				$sql    = 'select * from people_addresses where id=?';
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) { $this->exchangeArray($result->current()); }
                else { throw new \Exception('emails/unknown'); }
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setState('IN');
		}
	}

    /**
     * When repopulating with fresh data, make sure to clear all foreign key objects
     *
     * @Override
     */
    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);

        $this->person = null;
    }

    public function validate()
    {
		if (!$this->getPerson_id() || !$this->getType()) { throw new \Exception('missingRequiredFields'); }
    }

    public function save()   { parent::save(); }
    public function delete() { parent::delete(); }

	public function getId()      { return (int)parent::get('id'     ); }
	public function getType()    { return      parent::get('type'   ); }
	public function getAddress() { return      parent::get('address'); }
	public function getCity()    { return      parent::get('city'   ); }
	public function getState()   { return      parent::get('state'  ); }
	public function getZip()     { return      parent::get('zip'    ); }
	public function getX()       { return (int)parent::get('x'      ); }
	public function getY()       { return (int)parent::get('y'      ); }

	public function setType   (string $s) { parent::set('type',    $s); }
	public function setAddress(string $s) { parent::set('address', $s); }
	public function setCity   (string $s) { parent::set('city',    $s); }
	public function setState  (string $s) { parent::set('state',   $s); }
	public function setZip    (string $s) { parent::set('zip',     $s); }
	public function setX      (int    $x) { parent::set('x',       $x); }
	public function setY      (int    $y) { parent::set('y',       $y); }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $p);  }

    public function handleUpdate(array $post)
	{
		foreach (['type', 'address', 'city', 'state', 'zip'] as $f) {
			if (!empty($post[$f])) { parent::set($f, $post[$f]); }
			else                   { parent::set($f, null); }
		}
		foreach (['x', 'y'] as $f) {
			if (!empty($post[$f])) { parent::set($f, (int)$post[$f]); }
			else                   { parent::set($f, null); }
		}

	}


	public function __toString(): string { return $this->getAddress(); }
}
