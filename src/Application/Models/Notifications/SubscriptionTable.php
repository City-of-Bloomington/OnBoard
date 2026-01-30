<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

use Application\PdoRepository;

class SubscriptionTable extends PdoRepository
{
    public const TABLE = 'notification_subscriptions';
    public static $columns = ['person_id', 'committee_id', 'event'];
    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Subscription'); }
}
