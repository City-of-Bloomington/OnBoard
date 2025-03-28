<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

interface WarehouseInterface
{
    public static function meeting_info(int $committee_id, \DateTime $date): array;
}
