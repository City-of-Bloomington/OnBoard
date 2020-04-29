<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Web\Controller;
use Web\Block;
use Web\View;
use Application\Models\CommitteeTable;

class IndexController extends Controller
{
	public function index(): View
	{
        header('Location: '.BASE_URL.'/committees');
        exit();
        return $this->template;
	}
}
