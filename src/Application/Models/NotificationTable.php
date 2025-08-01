<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

class NotificationTable extends \Web\TableGateway
{
    public const TABLE = 'notifications';
    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Notification'); }
}
