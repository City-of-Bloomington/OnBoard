<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Legislation\Action;

use Web\Controller;
use Web\Block;
use Web\View;

class LegislationActionsController extends Controller
{
    public function update(): View
    {
        if (!empty($_REQUEST['id'])) {
            try { $action = new Action($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            if (!empty($_REQUEST['legislation_id']) && !empty($_REQUEST['type_id'])) {
                try {
                    $action = new Action();
                    $action->setLegislation_id($_REQUEST['legislation_id']);
                    $action->setType_id       ($_REQUEST['type_id'       ]);
                }
                catch (\Exception $e) {
                    unset($action);
                    $_SESSION['errorMessages'][] = $e;
                }
            }
        }

        if (isset($action)) {
            if (isset($_POST['legislation_id'])) {
                try {
                    $action->handleUpdate($_POST);
                    $action->save();
                    header('Location: '.BASE_URL.'/legislation/view?id='.$action->getLegislation_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('legislation/updateActionForm.inc', ['action'=>$action]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }
}
