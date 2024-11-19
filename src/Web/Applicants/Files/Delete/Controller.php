<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Files\Delete;

use Application\Models\ApplicantFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try {
                $file = new ApplicantFile($params['id']);
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
