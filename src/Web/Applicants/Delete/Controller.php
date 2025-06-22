<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Delete;

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
            $applicant->delete();
            header('Location: '.\Web\View::generateUrl('applicants.index'));
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
