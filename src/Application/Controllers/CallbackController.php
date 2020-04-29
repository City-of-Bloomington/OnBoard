<?php
/**
 * A simple controller for JSONP callback requests
 *
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Web\Controller;
use Web\View;

class CallbackController extends Controller
{
	public function index(): View
	{
		$this->template->setFilename('callback');
        return $this->template;
	}
}
