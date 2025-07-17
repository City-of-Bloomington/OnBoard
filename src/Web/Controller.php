<?php
/**
 * @copyright 2012-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

abstract class Controller
{
	protected const ITEMS_PER_PAGE = 20;
	protected $outputFormat;

	public function __construct()
	{
        $this->outputFormat = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
	}

	/**
     * Saves a new return_url into the SESSION
     *
     * @param string $default   Default url to use if none available in REQUEST or SESSION
     */
	public static function captureNewReturnUrl(?string $default=BASE_URL)
    {
        if (!empty($_REQUEST['return_url'])) { $_SESSION['return_url'] = $_REQUEST['return_url']; }
        if (!empty($_SESSION['return_url'])) { $_SESSION['return_url'] = $default; }
    }
}
