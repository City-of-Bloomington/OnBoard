<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

class DefinitionTable extends \Web\TableGateway
{
    public const TABLE = 'notification_definitions';
    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\Definition'); }
}
