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
}
