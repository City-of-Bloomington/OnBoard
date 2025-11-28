<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Add;

use Application\Models\Person;
use Web\People\Update\View as UpdateView;

class View extends \Web\View
{
    public function __construct(Person $p)
    {
        parent::__construct();

        $this->vars = [
            'person'      => $p,
            'states'      => UpdateView::states(),
            'yesno'       => UpdateView::yesno(),
            'return_url'  => parent::generateUri('people.index'),
            'breadcrumbs' => self::breadcrumbs()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/addForm.twig', $this->vars);
    }

    private static function breadcrumbs(): array
    {
        return [
            parent::_(['person', 'people', 10]) => parent::generateUri('people.index'),
            parent::_('person_add') => null
        ];
    }
}
