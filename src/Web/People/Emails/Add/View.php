<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Emails\Add;

use Application\Models\Email;

class View extends \Web\View
{
    public function __construct(Email $email)
    {
        parent::__construct();

        $this->vars = [
            'email' => $email
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/emails/updateForm.twig', $this->vars);
    }
}
