<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Info;

use Application\Models\Alternate;
use Web\Url;

class View extends \Web\View
{
    public function __construct(Alternate $a)
    {
        parent::__construct();

        $this->vars = [
            'alternate'   => $a,
            'committee'   => $a->getCommittee(),
            'actionLinks' => $this->actionLinks($a)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/alternates/info.twig', $this->vars);
    }

    private function actionLinks(Alternate $a): array
    {
        $links = [];
        $p     = ['return_url' => Url::current_url(BASE_HOST)];
        if (parent::isAllowed('alternates', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('alternates.update', ['id'=>$a->getId()]),
                'label' => parent::_('alternate_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('alternates', 'delete')) {
            $p['alternate_id'] = $a->getId();

            $links[] = [
                'url'   => parent::generateUri('alternates.delete', ['id'=>$a->getId()]),
                'label' => parent::_('alternate_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }
}
