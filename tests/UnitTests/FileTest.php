<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testCreateValidFilename()
    {
        $badFilename = "S!*&$^!";
        $goodFilename = "S";

        $this->assertEquals("S",     File::createValidFilename($badFilename));
        $this->assertEquals("S.pdf", File::createValidFilename("$badFilename.pdf"),        'Existing file extensions not preserved');
        $this->assertEquals("S.doc", File::createValidFilename("$badFilename.pdf", "doc"), 'New file extension not applied');
    }
}
