<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Committees;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class NoteTable extends TableGateway
{
	public function __construct() { parent::__construct('committee_notes', __namespace__.'\Note'); }
	protected $columns = ['committee_id', 'person_id'];
}
