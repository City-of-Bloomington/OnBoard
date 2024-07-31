<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Report;

use Application\Models\SeatTable;

class View extends \Web\View
{
    public function __construct(array $committees, array $members)
    {
        parent::__construct();

        $this->vars = [
            'committees' => $committees,
            'members'    => $members
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/committees/report.twig', $this->vars);
    }
}
