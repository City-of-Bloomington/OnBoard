<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Update;

class View extends \Web\View
{
    public function __construct(Report $report)
    {
        parent::__construct();

        $this->vars = [
            'report'    => $report,
            'committee' => $report->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/updateForm.twig', $this->vars);
    }
}
