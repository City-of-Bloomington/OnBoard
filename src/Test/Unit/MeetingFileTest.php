<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Models\MeetingFile;
use PHPUnit\Framework\TestCase;

class MeetingFileTest extends TestCase
{
    public function testDefaultDates()
    {
        $file = new MeetingFile();
        $this->assertNotEmpty($file->getUpdated());
    }
}
