<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Laminas\Db\Sql\Select;

class DefinitionTable extends \Web\TableGateway
{
    public const TABLE = 'notification_definitions';
    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Definition'); }

    public static function events(): array
    {
        return [
            'Web\Applicants\Apply::confirmation',
            'Web\Applicants\Apply::notice',
            'Web\MeetingFiles\Update::notice'
        ];
    }

    public function find($fields=null, $order=['event','committee_id'], $paginated=false, $limit=null)
    {
        $select = new Select(self::TABLE);

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'committee_id':
                        if ($v) {
                            $select->where([$k=>$v]);
                        }
                        else {
                            $select->where('committee_id is null');
                        }
                    break;

                    case 'event':
                        $select->where([$k=>$v]);
                    break;

                    default:
                        // Ignore other fields
                }
            }
        }
        return parent::performSelect($select, $order);
    }

    public function loadForSending(string $event, int $committee_id): ?Definition
    {
        $t = new DefinitionTable();
        $l = $t->find(['committee_id'=>$committee_id, 'event'=>$event]);
        if (count($l)) { return $l->current(); }
        else {
            $l = $t->find(['committee_id'=>null, 'event'=>$event]);
            if (count($l)) { return $l->current(); }
        }
        return null;
    }
}
