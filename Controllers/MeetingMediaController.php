<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\MeetingMedia;
use Application\Models\MeetingMediaTable;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class MeetingMediaController extends Controller
{
    public function index()
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);

                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);

                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $table = new MeetingMediaTable();
        $list  = $table->find($search);

        $this->template->blocks[] = new Block('meetingMedia/list.inc', [
            'media'     => $list,
            'committee' => isset($committee) ? $committee : null
        ]);
    }

    public function update()
    {
        if (!empty($_REQUEST['meetingMedia_id'])) {
            try { $media = new MeetingMedia($_REQUEST['meetingMedia_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            // New Media files must have a committee_id passed in.
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $committee = new Committee($_REQUEST['committee_id']);
                    $media = new MeetingMedia();
                    $media->setCommittee($committee);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }

        if (isset($media)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $media->handleUpdate($_POST);
                    if (isset($_FILES['mediafile'])) {
                        $media->setFile($_FILES['mediafile']);
                    }
                    $media->save();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $committe = $media->getCommittee();
            $this->template->title = $committee->getName();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
            $this->template->blocks[] = new Block('meetingMedia/updateForm.inc', ['media'=>$media]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function download()
    {
        if (!empty($_GET['meetingMedia_id'])) {
            try {
                $media = new MeetingMedia($_GET['meetingMedia_id']);
                $this->template->setFilename('media');
                $this->template->blocks[] = new Block('media/download.inc', ['media'=>$media]);
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_GET['meetingMedia_id'])) {
            try {
                $media = new MeetingMedia($_GET['meetingMedia_id']);
                $committe = $media->getCommittee();
                $media->delete();
                header('Location: '.BASE_URI.'/meetingMedia?committe_id='.$committe->getId());
                exit();
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
