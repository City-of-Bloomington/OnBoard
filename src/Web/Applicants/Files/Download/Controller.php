<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Files\Download;

use Application\Models\ApplicantFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_GET['applicantFile_id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { }
        }
        return new \Web\Views\NotFoundView();
    }
}
