<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Files\Add;

use Application\Models\ApplicantFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $person = $_SESSION['USER'];
        $file   = new ApplicantFile();
        $file->setPerson($person);

        parent::captureNewReturnUrl(\Web\View::generateUrl('profile.index'));

        if (isset($_FILES['applicantFile'])
            &&    $_FILES['applicantFile']['error'] === UPLOAD_ERR_OK) {

            $file->setUpdatedPerson($_SESSION['USER']);
            $file->setFile($_FILES['applicantFile']);
            $file->save();

            $return_url = parent::popCurrentReturnUrl();
            header("Location: $return_url");
            exit();
        }

        return new View($file, $_SESSION['return_url']);
    }
}
