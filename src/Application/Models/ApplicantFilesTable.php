<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class ApplicantFilesTable extends TableGateway
{
    const TABLE = 'applicantFiles';

	public function __construct() { parent::__construct(self::TABLE, __namespace__.'\ApplicantFile'); }

	public function find($fields=null, $order='updated desc', $paginated=false, $limit=null)
	{
		$select = new Select(self::TABLE);
		if ($fields) {
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
