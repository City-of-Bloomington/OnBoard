<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Site\Classes;

use Application\WarehouseInterface;
use Application\Models\Committee;
use Web\Database;

class WarehouseService implements WarehouseInterface
{
    public static $board_hearing_types = [
        // OnBoard board ID => EPL Hearing Type ID
         1 => '56de5f04-a95d-575a-f8e6-bd41132ed44f', // Common Council
         6 => '74495ab1-0314-396f-0dcd-8c3b114dbd8f', // Bicycle and Pedestrian Safety Commission
        16 => 'a9d28901-7488-b35d-5f90-6d855a384cc6', // Board of Housing Quality Appeals
        27 => '20cbb508-1423-3761-be3b-7b4ee1b46b9e', // Board of Public Works
         4 => 'e7ade46c-6088-4763-a368-3c0365db5ede', // Board of Zoning Appeals
        43 => 'e2e97809-01cc-61af-7e3f-9d119c4e6f81', // Hearing Officer
        56 => '80017e4b-64cc-31f4-66dd-ab90863de96b', // Parking Commission
        24 => '719f8217-c80e-4938-b6f1-6de9b12ab3e8', // Plan Commission
        48 => '4bc273e3-6eb6-49be-645c-ec7e560d501b', // Plat Committee
        31 => 'd0377282-3ec3-e9ae-6995-ec84cb65df21' // Traffic Commission
    ];

    public static function meeting_info(int $committee_id, \DateTime $date): array
    {
        $db      = Database::getConnection('warehouse');
        $out     = [];
        $type_id = self::$board_hearing_types[$committee_id];
        $d       = $date->format('Y-m-d');
        if (array_key_exists($committee_id, self::$board_hearing_types)) {
            $sql = "select h.hearing_id,
                           h.status,
                           h.start_date,
                           p.plan_id,
                           p.plan_num
                    from epl.plan_hearings h
                    join epl.plans         p on p.plan_id=h.plan_id
                    where h.type_id=? and h.start_date=?";
            $result = $db->createStatement($sql)->execute([$type_id, $d]);
            if (count($result)) {
                $plans = [];
                foreach ($result as $row) { $plans[] = $row; }
                $out['plans'] = $plans;
            }

            $sql = "select h.hearing_id,
                           h.status,
                           h.start_date,
                           p.permit_id,
                           p.permit_num
                    from epl.permit_hearings h
                    join epl.permits        p on p.permit_id=h.permit_id
                    where h.type_id=? and h.start_date=?";
            $result = $db->createStatement($sql)->execute([$type_id, $d]);
            if (count($result)) {
                $permits = [];
                foreach ($result as $row) { $permits[] = $row; }
                $out['permits'] = $permits;
            }
        }
        return $out;
    }
}
