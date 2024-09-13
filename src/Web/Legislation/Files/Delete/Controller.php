<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Files\Delete;

use Application\Models\Legislation\LegislationFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['legislationFile_id'])) {
            try {
                $file = new LegislationFile((int)$_GET['legislationFile_id']);
                $legislation_id = $file->getLegislation_id();
                $file->delete();
                $return_url = \Web\View::generateUrl('legislation.view')."?legislation_id=$legislation_id";
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { }
        }
        return new \Web\Views\NotFoundView();
    }
}
