<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Download;

use Application\Models\MeetingFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file = new MeetingFile($_GET['meetingFile_id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { }
        }

        return new \Web\Views\NotFoundView();
    }
}
