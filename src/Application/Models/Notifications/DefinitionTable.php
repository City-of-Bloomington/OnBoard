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

    public const APPLICATION_CONFIRMATION = 'application_confirmation';
    public const APPLICATION_NOTICE       = 'application_notice';
    public const MEETINGFILE_NOTICE       = 'meetingFile_notice';

    /**
     * Maps event names to the model objects use for template variables
     */
    public static $events = [
        self::APPLICATION_CONFIRMATION => 'Application\Models\Application',
        self::APPLICATION_NOTICE       => 'Application\Models\Application',
        self::MEETINGFILE_NOTICE       => 'Application\Models\MeetingFile'
    ];

    public function find(?array $fields=null, string|array|null $order=['event','committee_id'], ?bool $paginated=false, ?int $limit=null)
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
        if (!in_array($event, array_keys(self::$events))) {
            throw new \Exception('notifications/invalidEvent');
        }

        $l = $this->find(['committee_id'=>$committee_id, 'event'=>$event]);
        if (count($l)) { return $l->current(); }
        else {
            $l = $this->find(['committee_id'=>null, 'event'=>$event]);
            if (count($l)) { return $l->current(); }
        }
        return null;
    }
}
