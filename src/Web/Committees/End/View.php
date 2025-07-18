<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\End;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'committee' => $committee
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/endDateForm.twig', $this->vars);
    }
}
