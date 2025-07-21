<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Update;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url = \Web\View::generateUrl('profile.index');

        if (isset($_POST['firstname'])) {
            $_SESSION['USER']->handleUpdate($_POST);
            try {
                $_SESSION['USER']->save();

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new \Web\People\Update\View($_SESSION['USER'], $return_url);
    }
}
