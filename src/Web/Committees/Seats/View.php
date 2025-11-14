<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Seats;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Committee $committee, array $seats, array $search)
    {
        parent::__construct();

        $this->vars = [
            'committee' => $committee,
            'seats'     => $seats,
            'search'    => $search
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/seats.twig', $this->vars);
    }
}
