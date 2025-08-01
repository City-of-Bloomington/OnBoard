<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Info;

use Application\Models\Notification;

class View extends \Web\View
{
    public function __construct(Notification $n)
    {
        parent::__construct();

        $this->vars = [
            'notification' => $n,
            'actionLinks'  => self::actionLinks($n)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/info.twig', $this->vars);
    }

    private static function actionLinks(Notification $n): array
    {
        $links = [];
        if (parent::isAllowed('notifications', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.update', ['notification_id'=>$n->getId()]),
                'label' => parent::_('notification_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('notifications', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.delete', ['notification_id'=>$n->getId()]),
                'label' => parent::_('notification_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }
}
