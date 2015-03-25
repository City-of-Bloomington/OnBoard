<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
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
        'email'            => $c->getEmail(),
        'phone'            => $c->getPhone(),
        'address'          => $c->getAddress(),
        'city'             => $c->getCity(),
        'state'            => $c->getState(),
        'zip'              => $c->getZip(),
        'statutoryName'    => $c->getStatutoryName(),
        'statuteReference' => $c->getStatuteReference(),
        'statuteUrl'       => $c->getStatuteUrl(),
        'yearFormed'       => $c->getYearFormed(),
        'vacancy'          => $c->hasVacancy()
    ];
}
echo json_encode($data);
