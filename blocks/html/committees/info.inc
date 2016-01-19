<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$fields = [
    'type',
    'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'yearFormed', 'website',
    'email', 'phone', 'address', 'city', 'state', 'zip'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
}

if ($statuteUrl) {
    $statuteReference = "<a href=\"$statuteUrl\">$statuteReference</a>";
}
if ($website) {
    $website = "<a href=\"$website\">$website</a>";
}

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$contactInfo     = $markdown->transform($this->committee->getContactInfo());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$editButton = '';
if (Person::isAllowed('committees', 'edit')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        BASE_URI.'/committees/update?committee_id='.$this->committee->getId(),
        $this->_('edit'),
        'edit',
        ButtonLink::SIZE_ICON
    );
}
?>
<section class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h1 class="fn1-uiBlock-title"><?= $this->_('committee_info') ?></h1>
    <div class="fn1-uiBlock-headerTools">
      <?= $editButton ?>
    </div>
  </header>
  <div class="fn1-uiBlock-content">

    <dl class="fn1-output-field">
      <dt><?= $this->_('type') ?></dt>
      <dd><?= $type ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('year_formed') ?></dt>
      <dd><?= $yearFormed ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('statutoryName') ?></dt>
      <dd><?= $statutoryName ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('statuteReference') ?></dt>
      <dd><?= $statuteReference ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('website') ?></dt>
      <dd><?= $website ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_(['email', 'emails', 1]) ?></dt>
      <dd><?= $email ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('phone') ?></dt>
      <dd><?= $phone ?></dd>
    </dl>
    <dl class="fn1-output-field">
      <dt><?= $this->_('address') ?></dt>
      <dd><address><?= $address ?>
        <?= $city ?> <?= $state ?> <?= $zip ?>
        </address>
      </dd>
    </dl>

    <?= $description ?>

    <h2><?= $this->_('meetingSchedule') ?></h2>
    <?= $meetingSchedule ?>

    <h2><?= $this->_('contactInfo') ?></h2>
    <?= $contactInfo ?>
  </div>
</section>
