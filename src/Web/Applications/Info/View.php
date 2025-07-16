<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\Info;

use Application\Models\Application;
use Web\People\View\View as PeopleView;

class View extends \Web\View
{
    public function __construct(Application $a)
    {
        parent::__construct();

        $person = $a->getPerson();

        $this->vars = [
            'application' => $a,
            'committee'   => $a->getCommittee(),
            'person'      => $person,
            'emails'               => PeopleView::emails($person),
            'phones'               => PeopleView::phones($person),
            'applicantFiles'       => PeopleView::applicantFiles($person),
            'applications_current' => PeopleView::applications_current ($person),
            'applications_archived'=> PeopleView::applications_archived($person),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applications/info.twig', $this->vars);
    }
}
