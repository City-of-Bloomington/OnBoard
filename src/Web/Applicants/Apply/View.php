<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Applicant;
use Application\Models\ApplicantFile;
use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\Site;
use Web\File;

class View extends \Web\View
{
    public function __construct(Applicant $applicant, ?Committee $committee=null)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = File::maxUpload();

        $this->vars = [
            'applicant' => $applicant,
            'committee' => $committee,
            'help'      => Site::getContent('applyForm_help'),
            'committee_options'  => self::committee_options(),
            'committees_chosen'  => self::committees_chosen(),
            'citylimits_options' => $this->citylimits_options(),
            'referral_options'   => $this->referral_options(),
            'accept'      => self::mime_types(),
            'maxBytes'    => $maxBytes,
            'maxSize'     => $maxSize,
            'RECAPTCHA_SITE_KEY' => RECAPTCHA_SITE_KEY
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/applyForm.twig', $this->vars);
    }

    private static function committee_options(): array
    {
        $options = [];
        $table   = new CommitteeTable();
        $list    = $table->find(['current'=>true, 'takesApplications'=>true]);
        foreach ($list as $c) {
            $options[] = ['value'=>$c->getId(), 'label'=>$c->getName()];
        }
        return $options;
    }

    private static function committees_chosen(): array
    {
        $out = [];
        if (!empty($_REQUEST['committees'])) {
            foreach (array_keys($_REQUEST['committees']) as $id) {
                $out[] = (int)$id;
            }
        }
        return $out;
    }

    private function citylimits_options(): array
    {
        return [
            ['value'=>1, 'label'=>$this->_('yes')],
            ['value'=>0, 'label'=>$this->_('no' )],
        ];
    }

    private function referral_options(): array
    {
        $options = [['value'=>'']];
        foreach (Applicant::$referralOptions as $o) { $options[] = ['value'=>$o]; }
        return $options;
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (ApplicantFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }
}
