<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Notifications;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class EmailQueue extends TableGateway
{
	public function __construct() { parent::__construct('email_queue', __namespace__.'\Email'); }
	protected $columns = ['email', 'person_id', 'main'];
}
