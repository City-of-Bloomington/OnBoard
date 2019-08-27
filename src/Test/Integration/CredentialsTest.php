<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    public function testCredentialsExist()
    {
        $this->assertTrue(
            file_exists(SITE_HOME.'/credentials.json'),
            'Google credentials file is missing'
        );
    }
}
