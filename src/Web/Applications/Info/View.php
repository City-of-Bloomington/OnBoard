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
        $return_url = parent::generateUrl('applications.view', ['application_id'=>$a->getId()]);

        $this->vars = [
            'application' => $a,
            'committee'   => $a->getCommittee(),
            'return_url'  => $return_url,
            'person'      => $person,
            'emails'               => PeopleView::emails               ($person, $return_url),
            'phones'               => PeopleView::phones               ($person, $return_url),
            'applicantFiles'       => PeopleView::applicantFiles       ($person, $return_url),
            'applications_current' => PeopleView::applications_current ($person, $return_url),
            'applications_archived'=> PeopleView::applications_archived($person, $return_url),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applications/info.twig', $this->vars);
    }
}
