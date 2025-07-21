<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Emails\Add;

use Application\Models\Email;

class View extends \Web\View
{
    public function __construct(Email $email, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'email'      => $email,
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/emails/updateForm.twig', $this->vars);
    }
}
