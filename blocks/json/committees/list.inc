<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->committees
 */
$data = [];
foreach ($this->committees as $c) {
    $data[] = [
        'id'               => $c->getId(),
        'name'             => $c->getName(),
        'website'          => $c->getWebsite(),
        'statutoryName'    => $c->getStatutoryName(),
        'statuteReference' => $c->getStatuteReference(),
        'statuteUrl'       => $c->getStatuteUrl(),
        'yearFormed'       => $c->getYearFormed(),
        'vacancy'          => $c->hasVacancy()
    ];
}
echo json_encode($data);
