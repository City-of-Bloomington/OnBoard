<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Update;

use Application\Models\Reports\Report;
use Application\Models\File;

class View extends \Web\View
{
    public function __construct(Report $report)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = File::maxUpload();

        $this->vars = [
            'report'    => $report,
            'committee' => $report->getCommittee(),
            'accept'    => self::mime_types(),
            'maxBytes'  => $maxBytes,
            'maxSize'   => $maxSize
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/updateForm.twig', $this->vars);
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (Report::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }

}
