<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Application\PdoRepository;

class DefinitionTable extends PdoRepository
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

    public function find(array $fields=[], ?string $order='event, committee_id', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from '.self::TABLE;
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'committee_id':
                        if ($v) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }
                        else {
                            $where[] = 'committee_id is null';
                        }
                    break;

                    case 'event':
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                    break;

                    default:
                        // Ignore other fields
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function loadForSending(string $event, int $committee_id): ?Definition
    {
        if (!in_array($event, array_keys(self::$events))) {
            throw new \Exception('notifications/invalidEvent');
        }

        $l = $this->find(['committee_id'=>$committee_id, 'event'=>$event]);
        if (count($l['rows'])) { return $l['rows'][0]; }
        else {
            $l = $this->find(['committee_id'=>null, 'event'=>$event]);
            if (count($l['rows'])) { return $l['rows'][0]; }
        }
        return null;
    }
}
