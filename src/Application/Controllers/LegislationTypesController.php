<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Legislation\Type;
use Application\Models\Legislation\TypesTable;

use Web\Controller;
use Web\Block;
use Web\View;

class LegislationTypesController extends Controller
{
    public function index(): View
    {
        $table = new TypesTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('legislation/types.inc', ['types'=>$list]);
        return $this->template;
    }

    public function update(): View
    {
        if (!empty($_REQUEST['id'])) {
            try { $type = new Type($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $type = new Type(); }

        if (isset($type)) {
            if (isset($_POST['name'])) {
                try {
                    $type->handleUpdate($_POST);
                    $type->save();
                    header('Location: '.BASE_URL.'/legislationTypes');
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('legislation/updateTypeForm.inc', ['type'=>$type]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }
}
