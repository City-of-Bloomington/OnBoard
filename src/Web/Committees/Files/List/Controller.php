<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\List;

use Application\Models\Committee;
use Application\Models\CommitteeFile;
use Application\Models\CommitteeFilesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try { $committee = new Committee($params['committee_id']); }
        catch (\Exception $e) { return new \Web\Views\NotFoundView(); }

        $files = [];
        $table = new CommitteeFilesTable();
        $list  = $table->find(['committee_id' => $committee->getId()]);
        foreach ($list['rows'] as $f) { $files[] = $f; }

        return new View($files, $committee);
    }
}
