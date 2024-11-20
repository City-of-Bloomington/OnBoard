<?php
/**
 * Final step of the chooser process
 *
 * Many routes pop open a new window and start the [choose person] process.
 * Ultimately, the javascript function "setPerson" on the parent window must be
 * called, with the chosen person_id.  Most cases, the javascript can be executed
 * with an inline link.  However, if there's a multi-step process, such as when
 * needing to add a new person to the system, then we need a route to render
 * some HTML containing the javascript function.
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Callback;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['person_id'])) {
            // This must always be a number
            // Do not accept any text, as it is a XSS vector
            $person_id = (int)$_REQUEST['person_id'];
        }

        if (isset($person_id)) {
            return new View($person_id);
        }

        return new \Web\Views\NotFoundView();
    }
}


class View extends \Web\View
{
    public function __construct(int $person_id)
    {
        parent::__construct();
        $this->vars['person_id'] = $person_id;
    }

    public function render(): string
    {
        return $this->twig->render('html/people/callback.twig', $this->vars);
    }
}
