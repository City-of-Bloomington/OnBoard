<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\Add;

use Application\Models\Tag;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $tag = new Tag();
        if (isset($_POST['name'])) {
            try {
                $tag->handleUpdate($_POST);
                $tag->save();
                header('Location: ' . View::generateUrl('tags.list'));
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }
        return new View($tag);
    }
}
