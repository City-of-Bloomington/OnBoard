<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Emails\Add;

use Application\Models\Email;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $email = new Email();
        $email->setPerson($_SESSION['USER']);
        $return_url = \Web\View::generateUrl('profile.index');

        if (isset($_POST['email'])) {
            $email->handleUpdate($_POST);

            try  {
                $email->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($email, $return_url);
    }
}
