<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Update;

use Application\Models\Legislation\Legislation;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }
        else { $legislation = new Legislation(); }


        if (isset($legislation)) {
            if (!$legislation->getCommittee_id()) {
                if (!empty($_REQUEST['committee_id'])) {
                    try { $legislation->setCommittee_id($_REQUEST['committee_id']); }
                    catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
                }
            }

            if (!empty($_REQUEST['parent_id'])) {
                try {
                    $parent = new Legislation($_REQUEST['parent_id']);
                    $legislation->setParent_id   ($parent->getId());
                    $legislation->setCommittee_id($parent->getCommittee_id());
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            if (!empty($_REQUEST['type_id'])) {
                try { $legislation->setType_id($_REQUEST['type_id']); }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }
        }

        if (isset($legislation) && $legislation->getCommittee_id()) {
            $_SESSION['return_url'] =    !empty($_REQUEST['return_url'])
                                    ? urldecode($_REQUEST['return_url'])
                                    : ($legislation->getId()
                                        ? \Web\View::generateUrl('legislation.view').'?legislation_id='.$legislation->getId()
                                        : \Web\View::generateUrl('legislation.index'));

            if (isset($_POST['number'])) {
                try {
                    // Needed for the new Bootstrap boolean toggle
                    if (!isset($_POST['amendsCode'])) { $_POST['amendsCode'] = false; }

                    # Legislation::handleUpdate calls save automatically
                    $legislation->handleUpdate($_POST);


                    $return_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            return new View($legislation, $_SESSION['return_url']);
        }

        return new \Web\Views\NotFoundView();
    }
}
