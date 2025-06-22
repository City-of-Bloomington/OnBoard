<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Files\Delete;

use Application\Models\ApplicantFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_REQUEST['applicantFile_id']);
                $url  = \Web\View::generateUrl('applicants.view', ['id'=>$file->getApplicant_id()]);

                $file->delete();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { }
        }

        return new \Web\View\NotFoundView();
    }
}
