<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Update;

use Application\Models\Applicant;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['applicant_id'])) {
            try { $applicant = new Applicant($_REQUEST['applicant_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($applicant)) {
            if (isset($_POST['firstname'])) {
                try {
                    $applicant->handleUpdate($_POST);
                    $applicant->save();

                    $return_url = \Web\View::generateUrl('applicants.view', ['applicant_id'=>$applicant->getId()]);
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($applicant);
        }

        return new \Web\Views\NotFoundView();
    }
}
