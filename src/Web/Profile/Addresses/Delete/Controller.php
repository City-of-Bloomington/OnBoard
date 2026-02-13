<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Addresses\Delete;

use Application\Models\Address;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['address_id'])) {
            try { $address = new Address($params['address_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        if (!isset($address)) {
            return new \Web\Views\NotFoundView();
        }
        if ($address->getPerson_id() != $_SESSION['USER']->getId()) {
            return new \Web\Views\ForbiddenView();
        }

        try { $address->delete(); }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        $url = \Web\View::generateUrl('profile.index');
        header("Location: $url");
        exit();
    }
}
