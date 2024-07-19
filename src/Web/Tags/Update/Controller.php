<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\Update;

use Application\Models\Tag;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $tag = null;
        if (!empty($_REQUEST['id'])) {
            try { $tag = new Tag($_REQUEST['id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        if ($tag && isset($_POST['name'])) {
            try {
                $tag->handleUpdate($_POST);
                $tag->save();
                header('Location: ' . View::generateUrl('tags.list'));
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        return new View($tag);
    }
}
