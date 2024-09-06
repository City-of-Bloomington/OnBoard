<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Site\Content;

use Application\Models\Site;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $content = [];
        foreach (Site::$labels as $l) {
            $content[$l] = Site::getContent($l);
        }

        return new View($content);
    }
}
