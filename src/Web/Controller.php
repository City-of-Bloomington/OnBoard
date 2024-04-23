<?php
/**
 * @copyright 2012-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

abstract class Controller
{
	protected const ITEMS_PER_PAGE = 20;
	protected $template;

	public function __construct()
	{
		// Twig system
        $this->outputFormat = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';

        // Create the legacy Template
        $this->template = !empty($_REQUEST['format'])
                        ? new Template('default', $_REQUEST['format'])
                        : new Template('default');
	}

	/**
	 * Returns the full URL for a named route
	 *
	 * This loads the $ROUTES global variable and calls the
	 * generate function on it.
	 *
	 * @see https://github.com/auraphp/Aura.Router/tree/2.x
	 * @param string $route_name
	 * @param array $params
	 * @return string
	 */
	public static function generateUrl($route_name, $params=[])
	{
        global $ROUTES;
        return "https://$_SERVER[SERVER_NAME]".$ROUTES->generate($route_name, $params);
	}
}
