<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Web\Controller;
use Web\Template;
use Web\Block;
use Web\View;

use Application\Models\Person;

class LoginController extends Controller
{
	private $return_url;

	public function __construct()
	{
		parent::__construct();

		$this->return_url = !empty($_SESSION['return_url'])
            ? $_SESSION['return_url']
            : (!empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL);

        $_SESSION['return_url'] = $this->return_url;
	}

	/**
	 * Attempts to authenticate users via CAS
	 */
	public function index(): View
	{
		// If they don't have CAS configured, send them onto the application's
		// internal authentication system
		if (!defined('CAS')) {
            $url = View::generateUrl('login.login').'?return_url='.$this->return_url;
			header("Location: $url");
			exit();
		}

		\phpCAS::client(CAS_VERSION_2_0, CAS_SERVER, 443, CAS_URI, 'https://'.BASE_HOST);
		\phpCAS::setNoCasServerValidation();
		\phpCAS::forceAuthentication();
		// at this step, the user has been authenticated by the CAS server
		// and the user's login name can be read with phpCAS::getUser().

		// They may be authenticated according to CAS,
		// but that doesn't mean they have person record
		// and even if they have a person record, they may not
		// have a user account for that person record.
		try {
			$_SESSION['USER'] = new Person(\phpCAS::getUser());
			unset($_SESSION['return_url']);
			header("Location: {$this->return_url}");
			exit();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		$this->template->blocks[] = new Block('loginForm.inc',array('return_url'=>$this->return_url));
        return $this->template;
	}

	/**
	 * Attempts to authenticate users based on AuthenticationMethod
	 */
	public function login(): View
	{
		if (isset($_POST['username'])) {
			try {
				$person = new Person($_POST['username']);
				if ($person->authenticate($_POST['password'])) {
					$_SESSION['USER'] = $person;
                    unset($_SESSION['return_url']);
					header('Location: '.$this->return_url);
					exit();
				}
				else {
					throw new \Exception('invalidLogin');
				}
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
		$this->template->blocks[] = new Block('loginForm.inc',array('return_url'=>$this->return_url));
        return $this->template;
	}

	public function logout()
	{
		session_destroy();
		header('Location: '.$this->return_url);
		exit();
	}
}
