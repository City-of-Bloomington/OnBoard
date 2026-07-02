<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\AccessIndiana;

use Application\Models\Person;
use Application\Models\PeopleTable;
use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Service\Builder\AuthorizationServiceBuilder;
use Facile\OpenIDClient\Service\Builder\UserInfoServiceBuilder;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (empty($_SESSION['return_url'])) {
            $_SESSION['return_url'] = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;
        }

        // If they don't have OpenID configured, send them onto the application's
        // internal authentication system
        global $AUTHENTICATION, $REQUEST;
        if (empty($AUTHENTICATION['accessin']['client_id'])) {
            return new \Web\Views\NotFoundView();
        }

        $config   = $AUTHENTICATION['accessin'];
        $issuer   = (new IssuerBuilder())->build("$config[server]/.well-known/openid-configuration");
        $metadata = ClientMetadata::fromArray(['client_id'     => $config['client_id'    ],
                                               'client_secret' => $config['client_secret'],
                                               'redirect_uris' => [\Web\View::generateUrl('login.accessin')],
                                               'token_endpoint_auth_method' => 'client_secret_basic'
                                               ]);
        $service  = (new AuthorizationServiceBuilder())->build();
        $client   = (new ClientBuilder())
                    ->setIssuer($issuer)
                    ->setClientMetadata($metadata)
                    ->build();

        if (isset($_REQUEST['id_token'])) {
            $params  = $service->getCallbackParams($REQUEST, $client);
            $tokens  = $service->callback($client, $params);
            $idToken = $tokens->getIdToken();
            /** @var array<string, mixed> $claims */
            $claims  = $tokens->claims();

            $nonce   = $_SESSION['nonce'] ?? '';
            if (!isset($claims['nonce']) || $claims['nonce']!=$nonce) {
                $_SESSION['errorMessages'][] = 'noAccessAllowed';
                return new \Web\Views\ForbiddenView();
            }

            unset($_SESSION['nonce']);

            if (empty($claims['email_verified'])) {
                $_SESSION['errorMessages'][] = 'people/unknown';
                return new \Web\Views\ForbiddenView();
            }

            try {
                $person = self::findPerson($claims['preferred_username']);
                if (!$person) { $person = self::createPerson($claims); }

                $_SESSION['USER'] = $person;
                $return_url = $_SESSION['return_url'];
                unset($_SESSION['return_url']);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = 'people/unknown';
                return new \Web\Views\ForbiddenView();
            }
        }

        $_SESSION['nonce'] = bin2hex(random_bytes(32));
        $idp_url  = $service->getAuthorizationUri($client, [
                        'response_mode' => 'form_post',
                        'response_type' => 'id_token',
                        'scope'         => 'openid email profile',
                        'nonce'         => $_SESSION['nonce']
                    ]);
        header("Location: $idp_url");
        exit();
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
    private static function createPerson(array $claims): Person
    {
        $p = new Person();
        $p->setFirstname($claims['given_name'        ]);
        $p->setLastname ($claims['family_name'       ]);
        $p->setUsername ($claims['preferred_username']);
        $p->setRole('Public');

        $p->save();
        $p->saveEmail($claims['email']);
        return $p;
    }
}
