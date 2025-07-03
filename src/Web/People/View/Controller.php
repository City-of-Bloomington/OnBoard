<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\View;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['person_id'])) {
            try {
                $person = new Person($_REQUEST['person_id']);

                if (   !\Web\View::isAllowed('people', 'viewContactInfo')
                    && !$person->isInvolved()) {
                    return new \Web\Views\NotFoundView();
                }

                switch ($this->outputFormat) {
                    case 'json':
                        return new \Web\Views\JSONView([
                            'id'        => $person->getId(),
                            'firstname' => $person->getFirstname(),
                            'lastname'  => $person->getLastname(),
                            'website'   => $person->getWebsite(),
                            'username'  => $person->getUsername(),
                            'gender'    => $person->getGender(),
                            'race'      => $person->getRace()
                        ]);
                        break;

                    default:
                        return new View($person);
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return \Web\Views\NotFoundView();
    }
}
