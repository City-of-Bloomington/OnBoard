<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Delete;

use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['person_id'])) {
            try {
                $person = new Person($_REQUEST['person_id']);
                $person->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        header('Location: '.\Web\View::generateUrl('people.index'));
        exit();
        return new \Web\View\NotFoundView();
    }
}
