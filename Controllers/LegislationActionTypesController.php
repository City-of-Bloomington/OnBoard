<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Legislation\ActionType;
use Application\Models\Legislation\ActionTypesTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class LegislationActionTypesController extends Controller
{
    public function index()
    {
        $table = new ActionTypesTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('legislation/actionTypes.inc', ['types'=>$list]);
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $type = new Type($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $type = new ActionType(); }

        if (isset($type)) {
            if (isset($_POST['name'])) {
                try {
                    $type->handleUpdate($_POST);
                    $type->save();
                    header('Location: '.BASE_URL.'/legislationActionTypes');
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
    }
}
