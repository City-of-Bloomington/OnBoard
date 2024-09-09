<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Update;

use Application\Models\Reports\Report;

class View extends \Web\View
{
    public function __construct(Report $report)
    {
        parent::__construct();

        $upload_max_size  = ini_get('upload_max_filesize');
        $post_max_size    = ini_get('post_max_size');
        $upload_max_bytes = self::return_bytes($upload_max_size);
        $post_max_bytes   = self::return_bytes(  $post_max_size);

        if ($upload_max_bytes < $post_max_bytes) {
            $maxSize  = $upload_max_size;
            $maxBytes = $upload_max_bytes;
        }
        else {
            $maxSize  = $post_max_size;
            $maxBytes = $post_max_bytes;
        }

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

    private static function return_bytes($size): int
    {
        switch (substr($size, -1)) {
            case 'M': return (int)$size * 1048576;
            case 'K': return (int)$size * 1024;
            case 'G': return (int)$size * 1073741824;
            default:  return (int)$size;
        }
    }
}
