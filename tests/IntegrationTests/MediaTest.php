<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\File;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function setUp()
    {
        copy(__DIR__.'/testfile', SITE_HOME.'/test');
    }

    public function tearDown()
    {
        unlink(SITE_HOME.'/test');
    }

    public function testPDFConversion()
    {
        File::convertToPDF(SITE_HOME.'/test');
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $this->assertEquals('application/pdf', finfo_file($info, SITE_HOME.'/test'));
    }
}
