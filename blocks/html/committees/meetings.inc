<?php
/**
 * @copyright 2017-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Committee $htis->committee
 * @param array     $this->meetings
 * @param int       $this->year
 */
use Application\Models\MeetingFile;

use Blossom\Classes\Block;
use Blossom\Classes\Url;
?>
<section id="meetings">
    <header>
        <h1><?= $this->year.' '.$this->_(['meeting', 'meetings', count($this->meetings)]); ?></h1>
        <div class="tools">
        <?php
            $url = new Url(Url::current_url(BASE_HOST));
            $url->year = $this->year - 1;
            echo "<a href=\"$url\">Back</a>";

            $url->year = $this->year + 1;
            echo "<a href=\"$url\">Next</a>";
        ?>
        </div>
    </header>
    <?php
        $block = new Block('meetingFiles/meetingTypeFiles.inc', ['committee'=>$this->committee]);
        foreach ($this->meetings as $date=>$meeting) {
            $d = new \DateTime($date);
            echo "
            <div class=\"meeting\">
                <h2><span class=\"month\">{$d->format('F')}</span>
                    <span class=\"day\">{$d->format('j')}</span>
                </h2>
            ";

            $block->date = $d;
            $block->eventId = !empty($meeting['eventId']) ? $meeting['eventId'] : '';
            foreach (MeetingFile::$types as $type) {
                $block->type = $type;
                $block->files = !empty($meeting['files'][$type]) ? $meeting['files'][$type] : [];
                echo $block->render('html', $this->template);
            }
            echo "
            </div>
            ";
        }
    ?>
</section>
