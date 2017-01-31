<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $htis->committee
 * @param array     $this->meetings
 * @param int       $this->year
 */
use Application\Models\MeetingFile;
use Blossom\Classes\Url;
?>
<section>
    <header>
        <h1><?= $this->year.' '.$this->_(['meeting', 'meetings', count($this->events)]); ?></h1>
        <div class="tools">
        <?php
            $url = new Url(Url::current_url());
            $url->year = $this->year - 1;
            echo "<a href=\"$url\">Back</a>";

            $url->year = $this->year + 1;
            echo "<a href=\"$url\">Next</a>";
        ?>
        </div>
    </header>
    <dl>
    <?php
        foreach ($this->meetings as $date=>$meeting) {
            $d = new \DateTime($date);
            echo "
            <dt><span class=\"month\">{$d->format('F')}</span>
                <span class=\"day\">{$d->format('j')}</span>
            </dt>
            ";

            $action = BASE_URI.'/meetingFiles/update';
            $helper = $this->template->getHelper('field');
            $committee_id = $this->committee->getId();
            foreach (MeetingFile::$types as $type) {
                $fileinput = $helper->field([
                    'name'  => 'meetingFile',
                    'label' => $this->_('meetingFile'),
                    'type'  => 'file'
                ]);
                echo "
                <dd><dl><dt>$type</dt>
                        <dd><form method=\"post\" action=\"$action\" enctype=\"multipart/form-data\">
                            <fieldset>
                                <input name=\"committee_id\" type=\"hidden\" value=\"$committee_id\" />
                                <input name=\"eventId\"      type=\"hidden\" value=\"$meeting[eventId]}\" />
                                <input name=\"meetingDate\"  type=\"hidden\" value=\"{$d->format(DATE_FORMAT)}\" />
                                <input name=\"type\"         type=\"hidden\" value=\"$type\" />
                                $fileinput
                                <button type=\"submit\" class=\"upload\">{$this->_('upload')}</button>
                            </fieldset>
                            </form>
                        </dd>

                    </dl>
                </dd>
                ";
            }
        }
    ?>
    </dl>
</section>
