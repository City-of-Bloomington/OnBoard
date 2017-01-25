<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class ApplicantMediaTable extends TableGateway
{
	public function __construct() { parent::__construct('applicantMedia', __namespace__.'\ApplicantMedia'); }

	public function find($fields=null, $order='updated desc', $paginated=false, $limit=null)
	{
		$select = new Select('applicantMedia');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
