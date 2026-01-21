<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Committee;
use PHPUnit\Framework\TestCase;

class CommitteeTest extends TestCase
{
    public function testSetValidators()
    {
        $cityLimits = 'Application\Applications\Validators\CityLimits';

        $committee  = new Committee();
        $committee->setValidators([$cityLimits]);
        $validators = $committee->getValidators();
        $this->assertEquals([$cityLimits], array_keys($validators));
    }
}
