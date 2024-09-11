<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\List;

use Application\Models\ApplicantTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $applicants = [];
        $table = new ApplicantTable();
        $list  = $table->find();
        foreach ($list as $a) { $applicants[] = $a; }

        return new View($applicants);
    }
}
