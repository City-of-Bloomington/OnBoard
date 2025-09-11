<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications;

class View extends \Web\View
{
    private $template;

    public function __construct(string $template, $model)
    {
        parent::__construct();

        $this->template = $this->twig->createTemplate($template);

        $this->vars = [
            'o' => $model
        ];
    }

    public function render(): string
    {
        return $this->template->render($this->vars);
    }

    public static function actionLinksForSubscriptions(string $event, int $committee_id, string $return_url): array
    {
        $sub   = null;
        $links = [];
        if ( isset($_SESSION['USER'])) {
            $sub = $_SESSION['USER']->hasNotificationSubscription($event, $committee_id);
            if ($sub) {
                if (parent::isAllowed('profile.notifications', 'delete')) {
                    $links[] = [
                        'url'   => parent::generateUri('profile.notifications.delete', ['subscription_id'=>$sub->getId()])."?return_url=$return_url",
                        'label' => parent::_('notification_subscription_delete'),
                        'class' => 'notifications_off'
                    ];
                }
            }
            else {
                if (parent::isAllowed('profile.notifications', 'add')) {
                    $params  = http_build_query(['committee_id'=>$committee_id, 'event'=>$event, 'return_url'=>$return_url]);
                    $links[] = [
                        'url'   => parent::generateUri('profile.notifications.add')."?$params",
                        'label' => parent::_('notification_subscription_add'),
                        'class' => 'notifications'
                    ];
                }
            }
        }
        return $links;
    }
}
