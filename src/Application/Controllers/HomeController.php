<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Web\Controller;
use Web\View;

class HomeController extends Controller
{
	public function index(): View
	{
        header('Location: '.View::generateUrl('committees.index'));
        exit();
        return $this->template;
	}
}
