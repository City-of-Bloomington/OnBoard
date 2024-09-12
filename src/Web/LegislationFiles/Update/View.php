<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\LegislationFiles\Update;

use Application\Models\Legislation\LegislationFile;
use Web\File;

class View extends \Web\View
{
    public function __construct(LegislationFile $file, string $return_url)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = File::maxUpload();

        $this->vars = [
            'file'        => $file,
            'return_url'  => $return_url,
            'accept'      => self::mime_types(),
            'maxBytes'    => $maxBytes,
            'maxSize'     => $maxSize
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/updateFileForm.twig', $this->vars);
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (LegislationFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }
}
