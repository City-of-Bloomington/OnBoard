<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;

class LegislationTypesTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationTypes', __namespace__.'\LegislationType'); }
}
