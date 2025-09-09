<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Laminas\Db\Sql\Select;

class SubscriptionTable extends \Web\TableGateway
{
    public const TABLE = 'notification_subscriptions';
    public static $columns = ['person_id', 'committee_id', 'event'];
    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Subscription'); }
}
