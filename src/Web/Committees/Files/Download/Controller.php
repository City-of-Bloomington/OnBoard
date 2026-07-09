<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\Download;

use Application\Models\CommitteeFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['file_id'])) {
            try {
                $file = new CommitteeFile($_REQUEST['file_id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { }
        }

        return new \Web\Views\NotFoundView();
    }
}
