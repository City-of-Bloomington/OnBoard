<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\Update;

use Application\Models\Committee;
use Application\Models\CommitteeFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try { $file = new CommitteeFile($params['file_id']); }
        catch (\Exception $e) { return new \Web\Views\NotFoundView(); }

        if (isset($_POST['type'])) {
            try { self::saveAndRedirect($file); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($file);
    }

    /**
     * @throws \Exception
     */
    public static function saveAndRedirect(CommitteeFile $file)
    {
        $file->setUrl  ($_POST['url'  ]);
        $file->setType ($_POST['type' ]);
        $file->setTitle($_POST['title']);
        $file->setUpdatedPerson($_SESSION['USER']);

        // Before we save the file, make sure all the database information is correct
        $file->validateDatabaseInformation();
        // If they are editing an existing document, they do not need to upload a new file
        if (isset($_FILES['file']) && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
            $file->setFile($_FILES['file']);
        }

        $file->save();

        $return_url = \Web\View::generateUrl('committees.files.index', ['committee_id'=>$file->getCommittee_id()]);
        header("Location: $return_url");
        exit();
    }
}
