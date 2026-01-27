<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Committees;

use Application\PdoRepository;

class NoteTable extends PdoRepository
{
	public function __construct() { parent::__construct('committee_notes', __namespace__.'\Note'); }
	protected $columns = ['committee_id', 'person_id'];
}
