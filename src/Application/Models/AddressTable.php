<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class AddressTable extends PdoRepository
{
	public function __construct() { parent::__construct('people_addresses', __namespace__.'\Address'); }
	protected $columns = ['person_id', 'type', 'city', 'state', 'zip'];
}
