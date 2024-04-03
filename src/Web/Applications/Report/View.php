<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\Report;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(array $applicants, Committee $committee, array $seats)
    {
        parent::__construct();

        $this->vars = [
            'applicants' => $applicants,
            'committee'  => $committee,
            'seats'      => $seats
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/applications/report.twig', $this->vars);
    }
}
