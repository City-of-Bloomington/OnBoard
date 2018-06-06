<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;

class LegislationFilesTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationFiles', __namespace__.'\LegislationFile'); }
}
