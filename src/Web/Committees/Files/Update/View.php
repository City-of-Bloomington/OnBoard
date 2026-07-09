<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\Update;

use Application\Models\CommitteeFile;
use Application\Models\File;

class View extends \Web\View
{
    public function __construct(CommitteeFile $file)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = File::maxUpload();

        $this->vars = [
             'file'        => $file,
             'committee'   => $file->getCommittee(),
             'types'       => self::typeOptions(),
             'accept'      => self::mime_types(),
             'maxBytes'    => $maxBytes,
             'maxSize'     => $maxSize
         ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/files/form.twig', $this->vars);
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (CommitteeFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }

    private static function typeOptions(): array
    {
        $options = [];
        foreach (CommitteeFile::$types as $t) { $options[] = ['value'=>$t]; }
        return $options;
    }

}
