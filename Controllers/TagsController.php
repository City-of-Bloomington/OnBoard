<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Tag;
use Application\Models\TagsTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class TagsController extends Controller
{
    public function index()
    {
        $table = new TagsTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('tags/list.inc', ['tags'=>$list]);
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $tag = new Tag($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $tag = new Tag(); }

        if (isset($tag)) {
            if (isset($_POST['name'])) {
                try {
                    $tag->handleUpdate($_POST);
                    $tag->save();
                    header('Location: '.BASE_URL.'/tags');
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('tags/updateForm.inc', ['tag'=>$tag]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
