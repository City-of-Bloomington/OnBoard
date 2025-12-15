<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class DepartmentTable extends TableGateway
{
    public function __construct() { parent::__construct('departments', __namespace__.'\Department'); }

    public function find(?array $fields=null, string|array|null $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = new Select('departments');
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'committee_id':
                        $select->join(['c'=>'committee_departments'], 'departments.id=c.department_id', []);
                        $select->where(['c.committee_id'=>$value]);
                        break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $itemsPerPage, $currentPage);
    }
}
