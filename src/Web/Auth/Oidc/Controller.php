<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Oidc;

use Application\Models\Person;
use Jumbojett\OpenIDConnectClient;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (empty($_SESSION['return_url'])) {
            $_SESSION['return_url'] = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;
        }

        // If they don't have OpenID configured, send them onto the application's
        // internal authentication system
        global $AUTHENTICATION;
        if (empty($AUTHENTICATION['oidc']['client_id'])) {
            return new \Web\Views\NotFoundView();
        }

        $config = $AUTHENTICATION['oidc'];
        $oidc   = new OpenIDConnectClient($config['server'], $config['client_id'], $config['client_secret']);
        $oidc->addScope(['openid', 'allatclaims', 'profile']);
        $oidc->setAllowImplicitFlow(true);
        $oidc->setRedirectURL(\Web\View::generateUrl('login.index'));
        $success = $oidc->authenticate();
        if (!$success) {
            $_SESSION['errorMessages'][] = 'invalidLogin';
        }

        // at this step, the user has been authenticated by the OIDC server
        $info = $oidc->getVerifiedClaims();

        if (!$info->{$config['claims']['username']}) {
            $_SESSION['errorMessages'][] = 'ldap/unknownUser';
        }
        // They may be authenticated according to ADFS,
        // but that doesn't mean they have person record
        // and even if they have a person record, they may not
        // have a user account for that person record.
        try {
            $_SESSION['USER'] = new Person($info->{$config['claims']['username' ]});
            $return_url = $_SESSION['return_url'];
            unset($_SESSION['return_url']);
            header("Location: $return_url");
            exit();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
        }

        return new \Web\Views\ForbiddenView();
    }
}
