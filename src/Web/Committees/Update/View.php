<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Update;

use Application\Models\Committee;
use Application\Models\DepartmentTable;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();

        $departments = new DepartmentTable();

        $this->vars = [
            'committee'       => $committee,
            'committee_types' => self::committee_types(),
            'departments'     => $departments->find()
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/updateForm.twig", $this->vars);
    }

    private static function committee_types(): array
    {
        $out = [];
        foreach (Committee::$types as $t) { $out[] = ['value'=>$t]; }
        return $out;
    }
}
