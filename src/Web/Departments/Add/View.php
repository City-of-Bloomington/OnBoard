<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Departments\Add;

use Application\Models\Department;

class View extends \Web\View
{
    public function __construct(Department $department)
    {
        parent::__construct();

        $this->vars = [
            'department' => $department
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/departments/update.twig', $this->vars);
    }
}
