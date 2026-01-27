<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Notifications;

use Application\PdoRepository;

class EmailQueue extends PdoRepository
{
    const TABLE = 'email_queue';

    public function __construct() { parent::__construct('email_queue', __namespace__.'\Email'); }
	protected $columns = ['email', 'person_id', 'main', 'event', 'committee_id', 'sent'];

    public function find(array $fields=[], ?string $order='created desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from '.self::TABLE;
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (in_array($k, $this->columns)) {
                    switch ($k) {
                        case 'sent':
                            $where[] = !$v
                                     ? 'sent is null'
                                     : 'sent is not null';
                        break;
                        default:
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                    }
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
