<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Application $this->application
 */
declare (strict_types=1);

$committee    = $this->application->getCommittee()->getName();
$committee_id = $this->application->getCommittee_id();
$person       = $this->application->getApplicant()->getFullname();
$message      = sprintf($this->template->_('board_application_message', 'messages'), $person, $committee);
$url          = BASE_URL.'/committees/applications?committee_id='.$committee_id;

echo "
$message

$url
";
