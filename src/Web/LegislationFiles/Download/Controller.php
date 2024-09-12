<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\LegislationFiles\Download;

use Application\Models\Legislation\LegislationFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['legislationFile_id'])) {
            try {
                $id   = (int)$_GET['legislationFile_id'];
                if (!$id) { throw new \Exception('files/unknownFile'); }
                $file = new LegislationFile($id);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
