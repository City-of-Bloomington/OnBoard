<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Site\Update;

use Application\Models\Site;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty(     $_REQUEST['label'])
            && in_array($_REQUEST['label'], Site::$labels)) {

            if (isset($_POST['label'])) {
                try {
                    Site::saveContent($_POST);
                    header('Location: '.\Web\View::generateUrl('site.index'));
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new View($_REQUEST['label'], Site::getContent($_REQUEST['label']));
        }
        else {
            $_SESSION['errorMessages'][] = 'site/unknownLabel';
        }

        return new \Web\Views\NotFoundView();
    }
}
