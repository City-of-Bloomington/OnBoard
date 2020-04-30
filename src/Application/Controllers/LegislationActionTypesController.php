<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Legislation\ActionType;
use Application\Models\Legislation\ActionTypesTable;

use Web\Controller;
use Web\Block;
use Web\View;

class LegislationActionTypesController extends Controller
{
    public function index(): View
    {
        $table = new ActionTypesTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('legislation/actionTypes.inc', ['types'=>$list]);
        return $this->template;
    }

    public function update(): View
    {
        if (!empty($_REQUEST['id'])) {
            try { $type = new ActionType($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $type = new ActionType(); }

        if (isset($type)) {
            if (isset($_POST['name'])) {
                try {
                    $type->handleUpdate($_POST);
                    $type->save();
                    $return_url = View::generateUrl('legislationActionTypes.index');
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('legislation/updateActionTypeForm.inc', ['type'=>$type]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }
}
