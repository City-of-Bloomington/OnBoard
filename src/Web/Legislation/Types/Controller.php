<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Legislation\Types;

use Application\Models\Legislation\TypesTable;
use Application\Models\Legislation\Type;
use Web\Controller as BaseController;
use Web\View as BaseView;

class Controller extends BaseController
{
    public function index(): BaseView
    {
        $table = new TypesTable();
        $list = $table->find();
        return new View($list);
    }

    public function update(): BaseView
    {
        $type = !empty($_REQUEST['id']) ? new Type($_REQUEST['id']) : new Type();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type->handleUpdate($_POST);
            $type->save();
            header('Location: ' . BaseView::generateUrl('legislationTypes.index'));
            exit;
        }
        return new View($type, true);
    }
}
