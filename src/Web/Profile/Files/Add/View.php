<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Files\Add;

use Application\Models\ApplicantFile;

class View extends \Web\View
{
    public function __construct(ApplicantFile $f, string $return_url)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = ApplicantFile::maxUpload();

        $this->vars = [
            'person'     => $f->getPerson(),
            'file'       => $f,
            'return_url' => $return_url,
            'accept'     => self::mime_types(),
            'maxBytes'   => $maxBytes,
            'maxSize'    => $maxSize
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/profile/files/addForm.twig', $this->vars);
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (ApplicantFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }
}
