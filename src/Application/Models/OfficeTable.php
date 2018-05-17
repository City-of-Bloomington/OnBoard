<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class OfficeTable extends TableGateway
{
	public function __construct() { parent::__construct('offices', __namespace__.'\Office'); }

	public function find($fields=null, $order='startDate', $paginated=false, $limit=null)
	{
		$select = new Select('offices');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = \DateTime::createFromFormat('Y-m-d', $value);
						$select->where("startDate<='{$date->format('Y-m-d')}'");
						$select->where("(endDate is null or endDate>='{$date->format('Y-m-d')}')");
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
