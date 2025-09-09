<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Phones\Delete;

use Application\Models\Phone;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['phone_id'])) {
            try {
                $phone = new Phone($params['phone_id']);
                if ($phone->getPerson_id() == $_SESSION['USER']->getId()) {
                    $phone->delete();
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            $return_url = \Web\View::generateUrl('profile.index');
            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
