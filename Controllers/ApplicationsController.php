<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Application;
use Application\Models\ApplicationTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class ApplicationsController extends Controller
{
    public function index()
    {
        $table = new ApplicationTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('applications/list.inc', ['applications'=>$list]);
    }

    public function apply()
    {
        $application = new Application();

        if (isset($_POST['firstname'])) {
            try {
                $application->handleUpdate($_POST);
                $application->save();

                $this->template->blocks[] = new Block('applications/success.inc', ['application'=>$application]);

                return;
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        $this->template->blocks[] = new Block('applications/updateForm.inc', ['application'=>$application]);
    }
}