<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Tag;
use Application\Models\TagsTable;

use Web\Block;
use Web\Controller;
use Web\View;

class TagsController extends Controller
{
    public function index(): View
    {
        $table = new TagsTable();
        $list  = $table->find();

        $this->template->blocks[] = new Block('tags/list.inc', ['tags'=>$list]);
        return $this->template;
    }

    public function update(): View
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
                    header('Location: '.View::generateUrl('tags.index'));
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
        return $this->template;
    }
}
