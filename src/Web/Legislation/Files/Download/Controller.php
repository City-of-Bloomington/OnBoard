<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Files\Download;

use Application\Models\Legislation\LegislationFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislationFile_id'])) {
            try {
                $id   = (int)$_REQUEST['legislationFile_id'];
                if (!$id) { throw new \Exception('files/unknownFile'); }
                $file = new LegislationFile($id);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
