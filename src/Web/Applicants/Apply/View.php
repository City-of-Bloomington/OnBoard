<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Application;
use Application\Models\Committee;
use Application\Models\Site;

class View extends \Web\View
{
    public function __construct(array $post, Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'post'      => $post,
            'committee' => $committee,
            'help'      => Site::getContent('applyForm_help'),
            'citylimits_options' => $this->citylimits_options(),
            'referral_options'   => $this->referral_options()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/applyForm.twig', $this->vars);
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
        foreach (Application::$referralOptions as $o) { $options[] = ['value'=>$o]; }
        return $options;
    }
}
