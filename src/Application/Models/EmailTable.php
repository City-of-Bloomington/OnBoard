<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class EmailTable extends TableGateway
{
	public function __construct() { parent::__construct('people_emails', __namespace__.'\Email'); }
	protected $columns = ['email', 'person_id', 'main'];
}
