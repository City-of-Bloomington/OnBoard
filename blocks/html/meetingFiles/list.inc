<?php
/**
 * @copyright 2018-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->files
 * @param Committee         $this->committee (optional)
 * @param stdClass          $this->sort
 * @param array             $this->years   Available years for this committee
 */
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;
use Application\Models\Person;
use Web\Block;
use Web\Url;

$userCanEdit   = Person::isAllowed('meetingFiles', 'update');
$userCanDelete = Person::isAllowed('meetingFiles', 'delete');
?>
<section>
    <header>
        <h2><?= $this->_(['meetingFile', 'meetingFiles', count($this->files)]); ?></h2>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (!$this->disableButtons) {
                $url = new Url(Url::current_url(BASE_HOST));
                $url->format = 'csv';
                echo $helper->buttonLink($url, $this->_('csv'), 'download');
            }

            $helper = $this->template->getHelper('dropdown');
            if ($this->committee && $userCanEdit) {
                $actions      = [];
                $committee_id = $this->committee->getId();
                foreach (MeetingFile::$types as $type) {
                    $actions[] = [
                        'label' => $type,
                        'url'   => parent::generateUri('meetingFiles.update')."?type=$type;committee_id=$committee_id"
                    ];
                }
                echo $helper->dropdown($actions, $this->_('add'), 'meetingFile_add');
            }
        ?>
            <form method="get">
                <fieldset>
                <?php
                    if ($this->committee) {
                        echo "<input name=\"committee_id\" type=\"hidden\" value=\"{$this->committee->getId()}\" />";
                    }
                    $h       = $this->template->getHelper('field');
                    $options = [['value'=>'']];
                    foreach ($this->years as $y) { $options[] = ['value'=>$y]; }
                    echo $h->field([
                        'name'    => 'year',
                        'id'      => 'year',
                        'label'   => $this->_('year'),
                        'type'    => 'select',
                        'value'   => !empty($_GET['year']) ? (int)$_GET['year'] : '',
                        'options' => $options,
                        'attr'    => ['onchange'=>'this.form.submit();']
                    ]);
                ?>
                </fieldset>
            </form>
        </div>
    </header>
    <table>
    <thead>
        <tr><?php
                $url = new Url(Url::current_url(BASE_HOST));

                $fields = ['title', 'filename', 'meetingDate'];
                foreach ($fields as $f) {
                    $url->sort = ($this->sort->field == $f && $this->sort->direction == 'desc') ? "$f asc" : "$f desc";
                    $class     =  $this->sort->field == $f ? " class=\"sort {$this->sort->direction}\"" : '';
                    echo "<th><a href=\"$url\"$class>{$this->_($f)}</a></th>";
                }
            ?>
            <th><form method="get">
                    <fieldset>
                        <?php
                            foreach ($_GET as $k=>$v) {
                                if ($k != 'type') {
                                    $k = parent::escape($k);
                                    $v = parent::escape($v);
                                    echo "<input name=\"$k\" type=\"hidden\" value=\"$v\" />";
                                }
                            }
                        ?>
                        <select name="type" onChange="this.form.submit();">
                            <option value=""><?= $this->_('type'); ?></option>
                            <?php
                                $type = !empty($_GET['type']) ? $_GET['type'] : null;
                                foreach (MeetingFilesTable::$types as $t) {
                                    $selected = !empty($_GET['type']) && $_GET['type']==$t ? 'selected="true"' : '';
                                    echo "<option $selected>$t</option>";
                                }
                            ?>
                        </select>
                    </fieldset>
                </form>
            </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($this->files as $f) {
            $id       = $f->getId();
            $title    = parent::escape($f->getTitle());
            $filename = parent::escape($f->getFilename());
            $actions  = [];

            if ($userCanEdit) {
                $actions[] = [
                    'url'   => parent::generateUri('meetingFiles.update')."?meetingFile_id=$id",
                    'label' => $this->_('meetingFile_edit')
                ];
            }
            if ($userCanDelete) {
                $actions[] = [
                    'url'   => parent::generateUri('meetingFiles.delete')."?meetingFile_id=$id",
                    'label' => $this->_('meetingFile_delete')
                ];
            }
            $dropdown = $actions
                      ? $helper->dropdown($actions, $this->_('more'), "meetingFile_$id")
                      : '';

            $url = $f->getDownloadUri();
            echo "
            <tr><td>$title</td>
                <td><a href=\"$url\">$filename</a></td>
                <td>{$f->getMeetingDate(DATE_FORMAT)}</td>
                <td>{$f->getType()}</td>
                <td>$dropdown</td>
            </tr>
            ";
        }
    ?>
    </tbody>
    </table>
    <?php
        $block = new Block('pageNavigation.inc', ['paginator'=>$this->files]);
        echo $block->render('html', $this->template);
    ?>
</section>
