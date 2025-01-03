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
}
