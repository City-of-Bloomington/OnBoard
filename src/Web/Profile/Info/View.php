<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Info;

use Application\Models\Person;
use Web\People\View\View as PeopleView;

class View extends \Web\View
{
    public function __construct(Person $p)
    {
        parent::__construct();

        // Required for access control and url generation
        $_REQUEST['person_id'] = $p->getId();

        $this->vars = [
            'person' => $p,
            'emails'               => PeopleView::emails($p),
            'phones'               => PeopleView::phones($p),
            'applicantFiles'       => PeopleView::applicantFiles($p),
            'members'              => PeopleView::members ($p),
            'liaisons'             => PeopleView::liaisons($p),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/profile/info.twig', $this->vars);
    }
}
