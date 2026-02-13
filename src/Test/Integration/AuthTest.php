<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use PHPUnit\Framework\TestCase;
use Web\Auth\DepartmentAssociation;

class AuthTest extends TestCase
{
    public function testDepartmentAssociation()
    {
        $d = new DepartmentAssociation();
        foreach ($d::$params as $k=>$table) {
            $t = new $table();
            $v = $t->hasDepartment(1,1);
            $this->assertIsBool($v);
        }
    }
}
