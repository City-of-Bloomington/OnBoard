<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Emails\Add;

use Application\Models\Person;

use Application\Models\Email;

class View extends \Web\View
{
    public function __construct(Email $email, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'email'       => $email,
            'return_url'  => $return_url,
            'breadcrumbs' => self::breadcrumbs($email->getPerson())
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/emails/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Person $p): array
    {
        return [
            parent::_(['person', 'people', 10]) => parent::generateUri('people.index'),
            $p->getFullname() => parent::generateUri('people.view', ['person_id'=>$p->getId()]),
            parent::_('email_add') => null
        ];
    }
}
