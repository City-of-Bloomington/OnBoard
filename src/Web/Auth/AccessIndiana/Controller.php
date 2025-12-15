<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\AccessIndiana;

use Application\Models\Person;
use Application\Models\PeopleTable;
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

    /**
     * Find a person record that matches an email address
     *
     * Public user accounts use the email address as the username.
     * Access Indiana verifies that the person is in control of the email address,
     * so if we have an email address, we'll presume any person in the system
     * with that email address should be the person behind the browser.
     */
    private static function findPerson(string $email): ?Person
    {
        // Allow city staff to log in using Access Indiana
        list($username, $domain) = explode('@', $email);
        if ($domain == 'bloomington.in.gov') {
            try { return new Person($username); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                return null;
            }
        }

        // Look for existing person records
        try {
            $p = new PeopleTable();
            $r = $p->find(['username'=>$email]);
            if (count($r['rows'])==1) {
                // Existing public user account
                return $r['rows'][0];
            }

            $r = $p->find(['email'=>$email, 'user_account'=>false]);
            if (count($r['rows'])==1) {
                // Create public user account on existing person
                $person = $r['rows'][0];
                $person->setUsername($email);
                $person->setRole('Public');
                $person->save();
                return $person;
            }
        }
        catch (\Exception $e) { }

        return null;
    }

    /**
     * Create a new person record from Access Indiana claims
     */
    private static function createPerson($claims): Person
    {
        $p = new Person();
        $p->setFirstname($claims->given_name        );
        $p->setLastname ($claims->family_name       );
        $p->setUsername ($claims->preferred_username);
        $p->setRole('Public');

        $p->save();
        $p->saveEmail($claims->email);
        return $p;
    }
}
