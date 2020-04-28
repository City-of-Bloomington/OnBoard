<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);

namespace Application\Models\Reports;
use Web\TableGateway;

class ReportsTable extends TableGateway
{
    public $columns = ['id', 'title', 'reportDate', 'committee_id'];
	public function __construct() { parent::__construct('reports', __namespace__.'\Report'); }
}
