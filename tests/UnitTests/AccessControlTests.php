<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

require_once '../../bootstrap.inc';

class AccessControlTest extends PHPUnit_Framework_TestCase
{
    public function testPermissions()
    {
        $_SESSION['USER'] = new Person();
        $this->assertTrue(Person::isAllowed('committees', 'view'));

        $this->assertFalse(Person::isAllowed('committees', 'update'));
        $this->assertFalse(Person::isAllowed('seats',      'update'));

        $_SESSION['USER']->setRole('Staff');
        $this->assertTrue (Person::isAllowed('committees', 'update'));
        $this->assertFalse(Person::isAllowed('seats',      'delete'));
        $this->assertFalse(Person::isAllowed('committees', 'changeType'));

        $_SESSION['USER']->setRole('Administrator');
        $this->assertTrue(Person::isAllowed('committees', 'changeType'));
    }
}
