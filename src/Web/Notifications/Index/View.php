<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Index;

use Web\Notifications\Definitions\Update\View as DefinitionView;

class View extends \Web\View
{
    public function __construct(array $definitions,
                                array $emails,
                                array $search,
                                int   $total,
                                int   $itemsPerPage,
                                int   $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'definitions'  => $definitions,
            'actionLinks'  => self::actionLinks(),
            'emails'       => $emails,
            'total'        => $total,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'event'        => $search['event'       ] ?? '',
            'committee_id' => $search['committee_id'] ?? null,
            'events'       => DefinitionView::events(),
            'committees'   => DefinitionView::committees(),
            'breadcrumbs'  => self::breadcrumbs()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/info.twig', $this->vars);
    }

    private static function actionLinks(): array
    {
        $links = [];
        if (parent::isAllowed('notifications.definitions', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.definitions.add'),
                'label' => parent::_('notification_definition_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }

    private static function breadcrumbs(): array
    {
        return [
            parent::generateUri('settings.index')      => parent::_('settings'),
            parent::generateUri('notifications.index') => parent::_(['notification', 'notifications', 10])
        ];
    }
}
