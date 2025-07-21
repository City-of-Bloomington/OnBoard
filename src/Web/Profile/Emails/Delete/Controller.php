<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Emails\Delete;

use Application\Models\Email;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['email_id'])) {
            try { $email = new Email($params['email_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($email)) { return new \Web\Views\NotFoundView(); }

        try { $email->delete(); }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        $return_url = \Web\View::generateUrl('profile.index');
        header("Location: $return_url");
        exit();
    }
}
