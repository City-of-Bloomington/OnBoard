<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\AccessIndiana;

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
        if (empty($AUTHENTICATION['accessin']['client_id'])) {
            return new \Web\Views\NotFoundView();
        }

        $config = $AUTHENTICATION['accessin'];
        $oidc   = new OpenIDConnectClient($config['server'], $config['client_id'], $config['client_secret']);
        $oidc->setResponseTypes(['id_token']);
        $oidc->addScope(['openid', 'email', 'profile']);
        $oidc->setAllowImplicitFlow(true);
        $oidc->addAuthParam(['response_mode' => 'form_post']);
        $oidc->setRedirectURL(\Web\View::generateUrl('login.accessin'));

        $success = null;
        try { $success = $oidc->authenticate(); }
        catch (\Exception $e) { }
        if ($success) {
            // at this step, the user has been authenticated by the OIDC server
            $claims = $oidc->getVerifiedClaims();
            if ($claims->email_verified) {
                try {
                    $person = self::findPerson($claims->preferred_username);
                    if (!$person) { $person = self::createPerson($claims); }

                    $_SESSION['USER'] = $person;
                    $return_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = 'ldap/unknownUser'; }
            }
        }
        else { $_SESSION['errorMessages'][] = 'invalidLogin'; }

        return new \Web\Views\ForbiddenView();
    }

    private static function findPerson(string $email): ?Person
    {
        list($username, $domain) = explode('@', $email);

        // Allow city staff to log in using Access Indiana
        if ($domain == 'bloomington.in.gov') {
            try { return new Person($username); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                return null;
            }
        }

        // Look for existing person records
        try {
            $person = new Person($email);
            if (!$person->getUsername()) {
                // Create public user account on existing person
                $person->setUsername($email);
                $person->setRole('Public');
                $person->save();
            }
            return $person;
        }
        catch (\Exception $e) { }

        return null;
    }

    /**
     * Create a new person record from OIDC claims
     */
    private static function createPerson($claims): Person
    {
        $person = new Person();
        $person->setFirstname($claims->given_name        );
        $person->setLastname ($claims->family_name       );
        $person->setEmail    ($claims->email             );
        $person->setUsername ($claims->preferred_username);
        $person->setRole('Public');
    }
}
