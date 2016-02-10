<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Media;
use Application\Models\MediaTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class MediaController extends Controller
{
	private function loadMedia($id)
	{
		try {
            if (!$id) { throw new \Exception('media/unknown'); }
			$media = new Media($id);
		}
		catch (\Exception $e) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
			return null;
		}
		return $media;
	}

	public function index()
	{
	}

	public function download()
	{
		$media = $this->loadMedia($_GET['media_id']);
		if ($media) {
            $this->template->setFilename('media');
            $this->template->blocks[] = new Block('media/download.inc', ['media'=>$media]);
        }
	}

	public function delete()
	{
		$media = $this->loadMedia($_GET['media_id']);

		if ($media) {
            $return_url = BASE_URL.'/applicants/view?applicant_id='.$media->getApplicant_id();

            $media->delete();
            header("Location: $return_url");
            exit();
        }
	}
}
