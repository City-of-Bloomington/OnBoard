<?php
/**
 * Global, shared functions for all PHP web applications
 * @copyright 2006-2009 City of Bloomington, Indiana.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @package GlobalFunctions
 */
/**
 * Load classes on the fly as needed
 * @param string $class
 */
function __autoload($class)
{
	if (file_exists(APPLICATION_HOME."/classes/$class.php")) {
		require_once(APPLICATION_HOME."/classes/$class.php");
	}
	elseif (file_exists(FRAMEWORK."/classes/$class.php")) {
		require_once(FRAMEWORK."/classes/$class.php");
	}
}


/**
 * Provide nicely formatted error messages when PHP bombs out.
 */
function customErrorHandler ($errno, $errstr, $errfile, $errline)
{
	global $ERROR_REPORTING;

	if (isset($ERROR_REPORTING)) {
		if (in_array('PRETTY_PRINT',$ERROR_REPORTING)) {
			echo "
			<div id=\"errorMessages\">
				<p><em>from ".ADMINISTRATOR_NAME.":</em>
						There is an error in the code on this page that is through no fault of your own.
						Errors of this sort need to be fixed immediately, though.
						Please help us out by copying and pasting the following error message into an email and sending it to me at
						<a href=\"mailto:".ADMINISTRATOR_EMAIL."\">".ADMINISTRATOR_EMAIL."</a>.
				</p>
				<p><strong>Code Error:</strong>  Error on line $errline of file $errfile:</p>
				<p>$errstr</p>
			</div>
			";
		}
		if (in_array('EMAIL_ADMIN',$ERROR_REPORTING)) {
			$subject = APPLICATION_NAME.' Error';
			$message = "\t$_SERVER[REQUEST_URI]\n\nError on line $errline of file $errfile:\n$errstr\n\n";
			$message.= print_r(debug_backtrace(),true);
			mail(ADMINISTRATOR_EMAIL,$subject,$message,"From: apache@$_SERVER[SERVER_NAME]");
		}

		if (in_array('EMAIL_USER',$ERROR_REPORTING)
				&& isset($_SESSION['USER'])
				&& $_SESSION['USER']->getEmail()) {
			$subject = APPLICATION_NAME.' Error';
			$message = "\t$_SERVER[REQUEST_URI]\n\nError on line $errline of file $errfile:\n$errstr\n\n";
			$message.= print_r(debug_backtrace(),true);
			mail($_SESSION['USER']->getEmail(),
				 $subject,
				 $message,
				 "From: apache@$_SERVER[SERVER_NAME]");
		}
		if (in_array('SKIDDER',$ERROR_REPORTING)) {
			$message = "Error on line $errline of file $errfile:\n$errstr\n";
			$message.= print_r(debug_backtrace(),true);

			$skidder = curl_init(SKIDDER_URL);
			curl_setopt($skidder,CURLOPT_POST,true);
			curl_setopt($skidder,CURLOPT_HEADER,true);
			curl_setopt($skidder,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($skidder,
						CURLOPT_POSTFIELDS,
						array('application_id'=>SKIDDER_APPLICATION_ID,
							  'script'=>$_SERVER['REQUEST_URI'],
							  'type'=>$errstr,
							  'message'=>$message));
			curl_exec($skidder);
		}
	}
}
if (ERROR_REPORTING != 'PHP_DEFAULT') {
	set_error_handler('customErrorHandler');
}

/**
 * Object oriented exceptions are handled differently from other PHP errors.
 */
function customExceptionHandler($exception)
{
	global $ERROR_REPORTING;

	if (isset($ERROR_REPORTING)) {
		if (in_array('PRETTY_PRINT',$ERROR_REPORTING)) {
			echo "
			<div id=\"errorMessages\">
				<p><em>from ".ADMINISTRATOR_NAME.":</em>
						There is an error in the code on this page that is through no fault of your own.
						Errors of this sort need to be fixed immediately, though.
						Please help me out by copying and pasting the following error message into an email and sending it to me at
						<a href=\"mailto:".ADMINISTRATOR_EMAIL."\">".ADMINISTRATOR_EMAIL."</a>.
				</p>
				<p><strong>Uncaught exception:</strong>
					Exception on line {$exception->getLine()} of file {$exception->getFile()}:
				</p>
				<p>{$exception->getMessage()}</p>
			</div>
			";
		}
		if (in_array('EMAIL_ADMIN',$ERROR_REPORTING)) {
			$subject = APPLICATION_NAME.' Exception';
			$message = "\t$_SERVER[REQUEST_URI]\n\nException on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n\n";
			$message.= print_r(debug_backtrace(),true);
			mail(ADMINISTRATOR_EMAIL,$subject,$message,"From: apache@$_SERVER[SERVER_NAME]");
		}
		if (in_array('EMAIL_USER',$ERROR_REPORTING)
				&& isset($_SESSION['USER'])
				&& $_SESSION['USER']->getEmail()) {
			$subject = APPLICATION_NAME.' Exception';
			$message = "\t$_SERVER[REQUEST_URI]\n\nException on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n\n";
			$message.= print_r(debug_backtrace(),true);
			mail($_SESSION['USER']->getEmail(),
				 $subject,
				 $message,
				 "From: apache@$_SERVER[SERVER_NAME]");
		}
		if (in_array('SKIDDER',$ERROR_REPORTING)) {
			$message = "Error on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n";
			$message.= print_r(debug_backtrace(),true);

			$skidder = curl_init(SKIDDER_URL);
			curl_setopt($skidder,CURLOPT_POST,true);
			curl_setopt($skidder,CURLOPT_HEADER,true);
			curl_setopt($skidder,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($skidder,
						CURLOPT_POSTFIELDS,
						array('application_id'=>SKIDDER_APPLICATION_ID,
							  'script'=>$_SERVER['REQUEST_URI'],
							  'type'=>'Uncaught Exception',
							  'message'=>$message));
			curl_exec($skidder);
		}
	}
}
if (ERROR_REPORTING != 'PHP_DEFAULT') {
	set_exception_handler('customExceptionHandler');
}


/**
 * Makes sure the user is logged in.
 *
 * If a Role or an array of Roles is passed in, it will check
 * to make sure the user belongs to one of the given roles.
 * If the validation fails, the user will be bounced to the BASE_URL
 *
 * @param string $role Optional role name
 * @param array $roles Optional array of role names
 */
function verifyUser($roles=null)
{
	// Make sure they're logged in
	if (!isset($_SESSION['USER']) || !isset($_SESSION['IP_ADDRESS'])
		|| $_SESSION['IP_ADDRESS']!=$_SERVER['REMOTE_ADDR']) {
		// They're not logged in.  Boot them out to the login page
		print_r($_SESSION);
		$_SESSION['errorMessages'][] = new Exception('notLoggedIn');
		#header("Location: ".BASE_URL);
		exit();
	}

	// Check their roles against the required roles for the page
	if ($roles) {
		if (!$_SESSION['USER']->hasRole($roles)) {
			$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
			header('Location: '.BASE_URL);
			exit();
		}
	}
}

/**
 * Makes sure the user belongs to at least one of a set of roles
 * You can pass in either a single role name to check,
 * or an array of role names to check against.
 * @param string $role
 * @param array $roles
 * @return boolean
 */
function userHasRole($roles)
{
	if (isset($_SESSION['USER'])) {
		return $_SESSION['USER']->hasRole($roles);
	}
	return false;
}

/**
 * Browsers still use & when creating the url's when posting a form.
 * This will convert those into XHTML-compliant semicolons for using inside the markup
 *
 * @return string
 */
function getCurrentURL()
{
	return strtr($_SERVER['REQUEST_URI'],"&",";");
}


function isValidDate($date) {
	if (!preg_match("/\d{4}\-\d{1,2}\-\d{{1,2}/",$date)) {
		return false;
	}

	$date = explode("-",$date);
	if (count($date) != 3) {
		return false;
	}
	if (strlen($date[0]) != 4) {
		return false;
	}
	if (1>=$date[1] || $date[1]>=12) {
		return false;
	}
	if (1>=$date[2] || $date[2]>=31) {
		return false;
	}

	return true;
}
