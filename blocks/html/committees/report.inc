<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\SeatTable;
use Michelf\MarkdownExtra;
use Blossom\Classes\Block;

$name = self::escape($this->committee->getName());
?>
<article>
    <header>
        <h1><?= $name; ?></h1>
        <?php
            foreach ($this->committee->getStatutes() as $s) {
                echo "<h2><a href=\"{$s->getUrl()}\">{$s->getCitation()}</a></h2>";
            }
        ?>
    </header>
    <?php
        $markdown = new MarkdownExtra();
        $description     = $this->committee->getDescription();
        $meetingSchedule = $this->committee->getMeetingSchedule();
        foreach (['description', 'meetingSchedule'] as $f) {
            if ($$f) {
                $value = $markdown->transform($$f);
                echo "<dl><dt>{$this->_($f)}</dt><dd>$value</dd></dl>";
            }
        }

        if ($this->committee->getType() === 'seated') {
            $data = SeatTable::currentData(['committee_id'=>$this->committee->getId()]);
            $block = new Block('seats/data.inc', [
                'data'      => $data,
                'committee' => $this->committee,
                'title'     => $this->template->_(['current_member', 'current_members', count($data['results'])]),
                'disableButtons' => true
            ]);
        }
        else {
            $members = $this->committee->getMembers(['current'=>true]);
            $block = new Block('members/list.inc', [
                'committee' => $this->committee,
                'members'   => $members,
                'disableButtons' => true
            ]);
        }
        echo $block->render('html', $this->template);

        $temp = [];
        foreach ($this->committee->getSeats(['current'=>true]) as $s) {
            $requirements = $s->getRequirements();
            if ($requirements) {
                $temp[] = "<dd>{$s->getCode()}: $requirements</dd>";
            }
        }
        if (count($temp)) {
            $label = $this->_(['requirement', 'requirements', count($temp)]);
            echo "<dl><dt>$label</dt>";
            echo implode('', $temp);
            echo "</dl>";
        }
    ?>
</article>