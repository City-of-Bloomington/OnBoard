<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class AppointerTable extends PdoRepository
{
    public function __construct() { parent::__construct('appointers', __namespace__.'\Appointer'); }

    public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select a.* from appointers a';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'committee_id':
                        $joins['s'] = 'join seats s on a.id=s.appointer_id';
                        $where[]    = "s.$k=:$k";
                        $params[$k] = $v;
                    break;

                    case 'person_id':
                        $joins['s'] = 'join seats   s on a.id=s.appointer_id';
                        $joins[]    = 'join members m on s.id=m.seat_id';
                        $where[]    = "m.$k=:$k";
                        $params[$k] = $v;
                    break;

                    default:
                         $where[]   = "$k=:$k";
                        $params[]   = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
