<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

class Captcha
{
    public static function render()
    {
        echo "
        <script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api.js\"></script>
        <div class=\"g-recaptcha\" data-sitekey=\"".RECAPTCHA_SITE_KEY."\"
            data-callback=\"validateCaptcha\">
        </div>
        ";
    }

    public static function verify()
    {
        if (!isset($_POST['g-recaptcha-response'])) { return false; }

        $options = [
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS     => [
                'secret'   => RECAPTCHA_SERVER_KEY,
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        ];
        $verifierURL = "https://www.google.com/recaptcha/api/siteverify";
        $verifier = curl_init($verifierURL);
        curl_setopt_array($verifier, $options);
        $response = curl_exec($verifier);
        $header_size = curl_getinfo($verifier, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $json = json_decode($body, true);

        unset($_POST['g-recaptcha-response']);

        return (isset($json['success']) && $json['success']);
    }
}
