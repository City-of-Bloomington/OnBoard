<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Update;

use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Person $person, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'person'      => $person,
            'yesno'       => self::yesno(),
            'return_url'  => $return_url,
            'breadcrumbs' => self::breadcrumbs($person)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Person $p): array
    {
        return [
            parent::_(['person', 'people', 10]) => parent::generateUri('people.index'),
            $p->getFullname() => parent::generateUri('people.view', ['person_id'=>$p->getId()]),
            parent::_('person_edit') => null
        ];
    }

    public static function yesno(): array
    {
        return [
            ['value'=>1, 'label'=>parent::_('yes')],
            ['value'=>0, 'label'=>parent::_('no' )],
        ];
    }
}
