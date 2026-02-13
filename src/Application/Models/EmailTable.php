<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class EmailTable extends PdoRepository
{
	public function __construct() { parent::__construct('people_emails', __namespace__.'\Email'); }
	protected $columns = ['email', 'person_id', 'main'];
}
