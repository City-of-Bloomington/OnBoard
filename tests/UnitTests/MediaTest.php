<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Media;

include '../../bootstrap.inc';

class MediaTest extends PHPUnit_Framework_TestCase
{
    public function testCreateValidFilename()
    {
        $badFilename = "S!*&$^!";
        $goodFilename = "S";

        $this->assertEquals("S",     Media::createValidFilename($badFilename));
        $this->assertEquals("S.pdf", Media::createValidFilename("$badFilename.pdf"),        'Existing file extensions not preserved');
        $this->assertEquals("S.doc", Media::createValidFilename("$badFilename.pdf", "doc"), 'New file extension not applied');
    }
}
