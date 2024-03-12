<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Info;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();
        $links = [];
        if (parent::isAllowed('committees', 'update')) {
            $links['edit'] = [
                'url'   => parent::generateUri('committees.update').'?committee_id='.$committee->getId(),
                'label' => parent::_('edit')
            ];
        }
        if (parent::isAllowed('committees', 'end')) {
            $links['end'] = [
                'url'    => parent::generateUri('committees.end').'?committee_id='.$committee->getId(),
                'label'  => parent::_('committee_end')
            ];
        }

        $this->vars = [
            'committee'   => $committee,
            'actionLinks' => $links
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/info.twig", $this->vars);
    }
}
