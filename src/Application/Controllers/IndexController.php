<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Application\Models\CommitteeTable;

class IndexController extends Controller
{
	public function index()
	{
        header('Location: '.BASE_URL.'/committees');
        exit();
	}
}
