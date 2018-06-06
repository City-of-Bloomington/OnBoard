<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Legislation\Status;
use Application\Models\Legislation\StatusesTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class LegislationStatusesController extends Controller
{
    public function index()
    {
        $table = new StatusesTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('legislation/statuses.inc', ['statuses'=>$list]);
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $status = new Status($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $status = new Status(); }

        if (isset($status)) {
            if (isset($_POST['name'])) {
                try {
                    $status->handleUpdate($_POST);
                    $status->save();
                    header('Location: '.BASE_URL.'/legislationStatuses');
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('legislation/updateStatusForm.inc', ['status'=>$status]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            try { $status = new Status($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($status)) {
            $status->delete();
        }

        header('Location: '.BASE_URL.'/legislationStatuses');
        exit();
    }
}
