<?php
/**
 * Notification Models provide the templating variables for the messages
 *
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Notifications;

Interface Model
{
    public function getCommittee_id(): int;
}
