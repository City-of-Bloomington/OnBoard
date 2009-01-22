<?php
/**
 * Helper class for URL handling.  Parses URLs and allows adding parameters from variables.
 *
 * $url = new URL('/path/to/webpage.php?initialParameter=whatever');
 * $url->parameters['somevar'] = $somevar;
 * $url->somevar = $somevar;
 * echo $url->getURL();
 *
 * @copyright 2006-2009 City of Bloomington, Indiana.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class URL
{
	private $protocol;
	private $script;
	public $parameters = array();

	/**
	 * @param string $script
	 * @param array $parmeters
	 */
	public function __construct($script='',$parameters=array())
	{
		$this->parameters = $parameters;
		$this->setScript($script);
	}

	public function setScript($script)
	{
		$script = urldecode($script);

		if (preg_match('|://|',$script)) {
			list($protocol,$script) = explode('://',$script);
			$this->protocol = "$protocol://";
		}
		else {
			$this->protocol = $_SERVER['SERVER_PORT']==443 ? 'https://' : 'http://';
		}

		// Parse any parameters already in the script
		if (preg_match('/\?/',$script))
		{
			list($script,$parameters) = explode('?',$script);

			$parameters = preg_split('/[;&]/',$parameters);
			foreach ($parameters as $parameter) {
				if (preg_match('/=/',$parameter)) {
					list($field,$value) = explode('=',$parameter);
					if ($value) {
						$this->parameters[$field] = $value;
					}
				}
			}
		}

		$this->script = $script;
	}

	/**
	 * Returns just the base portion of the url
	 * @return string
	 */
	public function getScript() {
		return $this->script;
	}

	/**
	 * Returns the full, properly formatted and escaped URL
	 * @return string
	 */
	public function __toString() {
		return $this->getURL();
	}

	/**
	 * Returns the full, properly formatted and escaped URL
	 *
	 * @return string
	 */
	public function getURL()
	{
		$url = $this->protocol.$this->script;
		if (count($this->parameters)) {
			$url.= '?';
			foreach ($this->parameters as $key=>$value) {
				if (is_array($value)) {
					$url.= $this->smash($value,array($key));
				}
				else {
					$url.= urlencode($key).'='.urlencode($value).';';
				}
			}
		}
		return $url;
	}

	/**
	 * Returns just the protocol (http://, https://) portion
	 * @return string
	 */
	public function getProtocol() {
		if (!$this->protocol) {
			$this->protocol = 'http://';
		}
		return $this->protocol;
	}

	/**
	 * Sets the protocol for the URL (http, https)
	 * @param string $protocol
	 */
	public function setProtocol($string)
	{
		if (!preg_match('|://|',$string)) {
			$string .= '://';
		}
		$this->protocol = $string;
	}


	/**
	 * Converts a multi-dimensional array into a URL parameter string
	 */
	private function smash($array, $keyssofar)
	{
		$output = '';
		foreach ($array as $key => $value) {
			if (!is_array($value)) {
				$t = '';
				foreach ($keyssofar as $i=>$k) {
					if ($i) {
						$t.= '['.urlencode($k).']';
					}
					else {
						$t.=urlencode($k);
					}
				}

				$t.='['.urlencode($key).']';
				$output.= $t.'='.urlencode($value).';';
			}
			else {
				$t = array();
				foreach ($keyssofar as $k) {
					$t[] = $k;
				}

				$t[] = $key;
				$output.= $this->smash($value,$t);
			}
		}
		return $output;
	}


	/**
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		if (isset($this->parameters[$key])) { return $this->parameters[$key]; }
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key,$value)
	{
		$this->parameters[$key] = $value;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->parameters[$key]);
	}

	/**
	 * @param string $key
	 */
	public function __unset($key)
	{
		unset($this->parameters[$key]);
	}
}
