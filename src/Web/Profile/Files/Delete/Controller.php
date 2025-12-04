<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Files\Delete;

use Application\Models\ApplicantFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url = \Web\View::generateUrl('profile.index');

        if (!empty($_REQUEST['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_REQUEST['applicantFile_id']);

                if ($file->getPerson_id() == $_SESSION['USER']->getId()) {
                    $file->delete();
                }

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { }
        }

        return new \Web\Views\NotFoundView();
    }
}
