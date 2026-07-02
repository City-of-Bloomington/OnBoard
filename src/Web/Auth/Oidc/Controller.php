<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Oidc;

use Application\Models\Person;
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
        if (empty($AUTHENTICATION['oidc']['client_id'])) {
            return new \Web\Views\NotFoundView();
        }

        $config   = $AUTHENTICATION['oidc'];
        $issuer   = (new IssuerBuilder())->build("$config[server]/.well-known/openid-configuration");
        $metadata = ClientMetadata::fromArray(['client_id'     => $config['client_id'    ],
                                               'client_secret' => $config['client_secret'],
                                               'redirect_uris' => [\Web\View::generateUrl('login.cob')],
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

            if (empty($claims[$config['claims']['username']])) {
                $_SESSION['errorMessages'][] = 'ldap/unknownUser';
                return new \Web\Views\ForbiddenView();
            }

            try {
                $user = new Person($claims[$config['claims']['username']]);
                $_SESSION['USER'] = $user;
                $return_url       = $_SESSION['return_url'];
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
                        'nonce'         => $_SESSION['nonce']
                    ]);
        header("Location: $idp_url");
        exit();
    }
}
