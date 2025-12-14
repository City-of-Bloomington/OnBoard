<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Notifications;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class EmailQueue extends TableGateway
{
    const TABLE = 'email_queue';

    public function __construct() { parent::__construct('email_queue', __namespace__.'\Email'); }
	protected $columns = ['email', 'person_id', 'main', 'event', 'committee_id', 'sent'];

    public function find(?array $fields=null, string|array|null $order='created desc', ?bool $paginated=false, ?int $limit=null)
    {
        $select = new Select(self::TABLE);
        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (in_array($k, $this->columns)) {
                    switch ($k) {
                        case 'sent':
                            if (!$v) {
                                $select->where(['sent is null']);
                            }
                            else {
                                $select->where(['sent is not null']);
                            }
                        break;
                        default:
                            $select->where([$k=>$v]);
                    }
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }
}
