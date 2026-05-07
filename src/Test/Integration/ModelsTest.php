<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Database;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;


class ModelsTest extends TestCase
{
    public static function models(): array
    {
        return [
            ['\Application\Models\Address'],
            ['\Application\Models\Alternate'],
            ['\Application\Models\ApplicantFile'],
            ['\Application\Models\Application'],
            ['\Application\Models\Appointer'],
            ['\Application\Models\Committee'],
            ['\Application\Models\Committees\Note'],
            ['\Application\Models\CommitteeHistory'],
            ['\Application\Models\CommitteeStatute'],
            ['\Application\Models\Department'],
            ['\Application\Models\Email'],
            ['\Application\Models\Legislation\Action'],
            ['\Application\Models\Legislation\ActionType'],
            ['\Application\Models\Legislation\Legislation'],
            ['\Application\Models\Legislation\LegislationFile'],
            ['\Application\Models\Legislation\Status'],
            ['\Application\Models\Legislation\Type'],
            ['\Application\Models\Liaison'],
            ['\Application\Models\Meeting'],
            ['\Application\Models\MeetingFile'],
            ['\Application\Models\Member'],
            ['\Application\Models\Notifications\Definition'],
            ['\Application\Models\Notifications\Email'],
            ['\Application\Models\Notifications\Subscription'],
            ['\Application\Models\Office'],
            ['\Application\Models\Person'],
            ['\Application\Models\Phone'],
            ['\Application\Models\Reports\Report'],
            ['\Application\Models\Seat'],
            ['\Application\Models\Term']
        ];
    }

    #[DataProvider('models')]
    public function testConstructors(string $class)
    {
        $pdo = Database::getConnection();
        $tab = $class::TABLENAME;

        $q    = $pdo->query("select * from $tab limit 1");
        $res  = $q->fetchAll(\PDO::FETCH_ASSOC);

        $o    = new $class($res[0]['id']);
        $this->assertTrue(self::compare($res[0], $o->getData()));
    }

    /**
     * Make sure all the database fields are present in the loaded Model
     *
     * Model data may contain additional fields, this only compares the fields
     * from the database.
     */
    private static function compare(array $db, array $model): bool
    {
        foreach ($db as $k=>$v) {
            if ($model[$k] != $v) { return false; }
        }
        return true;
    }
}
