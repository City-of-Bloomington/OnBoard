<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Email\Info;

use Application\Models\Notifications\Email;

class View extends \Web\View
{
    public function __construct(Email $e)
    {
        parent::__construct();

        $this->vars = [
            'email' => $e
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/email/info.twig', $this->vars);
    }
}
