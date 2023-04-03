<?php
/**
 * @copyright 2018-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->files The search results
 * @param int      $this->committee_id   The chosen committee
 * @param int      $this->year           The chosen year
 * @param string   $this->type           The chosen file type
 * @param DateTime $this->startDate      The chosen start date
 * @param DateTime $this->endDate        The chosen end date
 * @param stdClass $this->sort           The chosen sorting
 * @param array    $this->committees     List of committees to choose from
 * @param array    $this->types          List of file types to choose from
 * @param array    $this->years          List of years to choose from
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
        <?php if (!$this->committee_id) { echo "<h2>{$this->_('meetingFile_search')}</h2>"; } ?>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (!$this->disableButtons) {
                $url = new Url(Url::current_url(BASE_HOST));
                $url->format = 'csv';
                echo $helper->buttonLink($url, $this->_('csv'), 'download');
            }

            $helper = $this->template->getHelper('dropdown');
            if ($this->committee_id && $userCanEdit) {
                $actions      = [];
                foreach ($this->types as $t) {
                    $actions[] = [
                        'label' => $t,
                        'url'   => parent::generateUri('meetingFiles.update')."?type=$t;committee_id={$this->committee_id}"
                    ];
                }
                echo $helper->dropdown($actions, $this->_('add'), 'meetingFile_add');
            }
        ?>
            <form method="get">
                <fieldset>
                <?php
                    $h       = $this->template->getHelper('field');
                    $options = [['value'=>'', 'label'=>$this->_('committees_all')]];
                    foreach ($this->committees as $c) { $options[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
                    echo $h->field([
                        'name'    => 'committee_id',
                        'id'      => 'committee_id',
                        'label'   => $this->_('committee'),
                        'type'    => 'select',
                        'value'   => $this->committee_id ?? null,
                        'options' => $options,
                        'attr'    => ['onchange'=>'this.form.submit();']
                    ]);

                    $options = [['value'=>'']];
                    foreach ($this->types as $t) { $options[] = ['value'=>$t]; }
                    echo $h->field([
                        'name'    => 'type',
                        'id'      => 'type',
                        'label'   => $this->_('type'),
                        'type'    => 'select',
                        'value'   => $this->type ?? null,
                        'options' => $options,
                        'attr'    => ['onchange'=>'this.form.submit();']
                    ]);

                    echo $h->field([
                        'name'     => 'startDate',
                        'id'       => 'startDate',
                        'label'    => $this->_('startDate'),
                        'value'    => $this->startDate ? $this->startDate : null,
                        'type'     => 'date'
                    ]);

                    echo $h->field([
                        'name'     => 'endDate',
                        'id'       => 'endDate',
                        'label'    => $this->_('endDate'),
                        'value'    => $this->endDate ? $this->endDate : null,
                        'type'     => 'date'
                    ]);

                    $options = [['value'=>'']];
                    foreach ($this->years as $y) { $options[] = ['value'=>$y]; }
                    echo $h->field([
                        'name'    => 'year',
                        'id'      => 'year',
                        'label'   => $this->_('year'),
                        'type'    => 'select',
                        'value'   => $this->year ?? null,
                        'options' => $options,
                        'attr'    => ['onchange'=>'this.form.submit();']
                    ]);
                ?>
                <button type="submit"><?= $this->_('search'); ?></button>
                </fieldset>
            </form>
        </div>
    </header>
    <table>
    <thead>
        <tr><?php
                $url = new Url(Url::current_url(BASE_HOST));

                $fields = ['meetingDate', 'type', 'committee', 'title'];
                foreach ($fields as $f) {
                    if (in_array($f, MeetingFilesTable::$sortableFields)) {
                        $url->sort = ($this->sort->field == $f && $this->sort->direction == 'desc') ? "$f asc" : "$f desc";
                        $class     =  $this->sort->field == $f ? " class=\"sort {$this->sort->direction}\"" : '';
                        echo "<th><a href=\"$url\"$class>{$this->_($f)}</a></th>";
                    }
                    else {
                        echo "<th>{$this->_($f)}</th>";
                    }
                }
            ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($this->files as $f) {
            $id        = $f->getId();
            $committee = parent::escape($f->getCommittee()->getName());
            $title     = parent::escape($f->getTitle());
            $filename  = parent::escape($f->getFilename());
            $actions   = [];

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
            <tr><th>{$f->getMeetingDate(DATE_FORMAT)}</th>
                <td><a href=\"$url\">{$f->getType()}</a></td>
                <td><a href=\"$url\">$committee</a></td>
                <td><a href=\"$url\">$title</a></td>
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
