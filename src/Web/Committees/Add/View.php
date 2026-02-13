<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Add;

use Application\Applications\Validators;
use Application\Models\Committee;
use Application\Models\DepartmentTable;
use Web\Committees\Update\View as UpdateView;

class View extends \Web\View
{
    public function __construct(Committee $c)
    {
        parent::__construct();

        $d = new DepartmentTable();

        $this->vars = [
            'committee'       => $c,
            'committee_types' => UpdateView::committee_types(),
            'departments'     => UpdateView::departments(),
            'validators'      => self::validators()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/addForm.twig', $this->vars);
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function validators(): array
    {
        $out = [];
        foreach (Validators::find() as $class) {
            $out[] = ['value'=>$class, 'label'=>$class::NAME];
        }
        return $out;
    }
}
