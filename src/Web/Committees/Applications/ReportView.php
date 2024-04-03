<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\Committee;
use Web\View;

class ReportView extends View
{
    public function __construct(Committee $committee,
                                array     $current_applications,
                                array     $archived_applications,
                                array     $seats)
    {
        parent::__construct();

        $this->vars = [
            'committee'             => $committee,
            'seats'                 => $seats,
            'current_applications'  => $current_applications,
            'archived_applications' => $archived_applications
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/applications/reportForm.twig', $this->vars);
    }
}
