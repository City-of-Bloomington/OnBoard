<?php
/**
 * @copyright 2012-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

abstract class Controller
{
	protected const ITEMS_PER_PAGE = 20;
	protected $outputFormat;
    protected $valid_output_formats = ['html'];

	public function __construct()
	{
        $this->outputFormat = !empty($_REQUEST['format']) && in_array($_REQUEST['format'], $this->valid_output_formats)
                            ? $_REQUEST['format']
                            : 'html';
	}

	/**
     * Saves a new return_url into the SESSION
     *
     * @param string $default   Default url to use if none available in REQUEST or SESSION
     */
	public static function captureNewReturnUrl(?string $default=BASE_URL)
    {
        if (!empty($_REQUEST['return_url'])) { $_SESSION['return_url'] = $_REQUEST['return_url']; }
        if ( empty($_SESSION['return_url'])) { $_SESSION['return_url'] = $default; }
    }

    /**
     * Returns the current return_url from the SESSION
     */
    public static function popCurrentReturnUrl(): string
    {
        if (isset( $_SESSION['return_url'])) {
            $url = $_SESSION['return_url'];
            unset( $_SESSION['return_url']);
            return $url;
        }
        return BASE_URL;
    }
}
