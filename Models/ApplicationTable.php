<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class ApplicationTable extends TableGateway
{
	public function __construct() { parent::__construct('applications', __namespace__.'\Application'); }

	public function find($fields=null, $order=['p.lastname', 'p.firstname'], $paginated=false, $limit=null)
	{
		$select = new Select(['a'=>'applications']);
		$select->join(['p'=>'applicants'], 'a.applicant_id=p.id', []);

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $value);
						$select->where("(a.archived is null or a.archived>='$date')");
						break;

					default:
						$select->where([$key=>$value]);
                }
            }
        }
		return parent::performSelect($select, $order, $paginated, $limit);
    }
}
