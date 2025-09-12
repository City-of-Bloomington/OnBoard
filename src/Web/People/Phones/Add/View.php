<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Phones\Add;

use Application\Models\Phone;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Phone $phone, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'phone'       => $phone,
            'return_url'  => $return_url,
            'breadcrumbs' => self::breadcrumbs($phone->getPerson())
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/phones/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Person $p): array
    {
        return [
            parent::_(['person', 'people', 10]) => parent::generateUri('people.index'),
            $p->getFullname() => parent::generateUri('people.view', ['person_id'=>$p->getId()]),
            parent::_('phone_add') => null
        ];
    }
}
