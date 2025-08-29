<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\Info;

use Application\Models\Notifications\Definition;

class View extends \Web\View
{
    public function __construct(Definition $d)
    {
        parent::__construct();

        $this->vars = [
            'definition'  => $d,
            'actionLinks' => self::actionLinks($d)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/definitions/info.twig', $this->vars);
    }

    private static function actionLinks(Definition $d): array
    {
        $links = [];
        if (parent::isAllowed('notifications.definitions', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.definitions.update', ['definition_id'=>$d->getId()]),
                'label' => parent::_('notification_definition_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('notifications.definitions', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.definitions.delete', ['definition_id'=>$d->getId()]),
                'label' => parent::_('notification_definition_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }
}
