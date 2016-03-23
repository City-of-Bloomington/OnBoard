<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class CommitteeStatuteTable extends TableGateway
{
	public function __construct() { parent::__construct('committeeStatutes', __namespace__.'\CommitteeStatute'); }
}