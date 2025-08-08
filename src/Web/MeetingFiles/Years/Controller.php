<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Years;

use Application\Models\Committee;
use Application\Models\MeetingFilesTable;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'json'];

    public function __invoke(array $params): \Web\View
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);
                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        $table = new MeetingFilesTable();
        $years = $table->years($search);

        switch ($this->outputFormat) {
            case 'json':
                return new \Web\Views\JSONView($years);
            break;

            default:
                return new View($years, $committee ?? null);
        }

    }
}
