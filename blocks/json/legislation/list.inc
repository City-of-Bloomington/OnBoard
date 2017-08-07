<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array       $this->list   An array of Legislation objects
 * @param Legislation $this->parent The parent legislation for the list
 */
use Application\Models\Person;
use Application\Models\Legislation\ActionTypesTable;
use Application\Models\Legislation\TypesTable;

use Blossom\Classes\Url;

$output = [];

foreach ($this->list as $l) {
    $actions = [];
    foreach ($l->getActions() as $a) {
        $actions[] = [
            'name'    => $a->getType()->getName(),
            'date'    => $a->getActionDate(),
            'outcode' => $a->getOutcome(),
            'vote'    => $a->getVote()
        ];
    }

    $files = [];
    foreach ($l->getFiles() as $f) {
        $files[] = [
            'url' => BASE_URL.'/legislationFiles/download?id='.$f->getId()
        ];
    }

    $output[] = [
        'committee'  => $l->getCommittee()->getName(),
        'type'       => $l->getType()->getName(),
        'number'     => $l->getNumber(),
        'year'       => $l->getYear(),
        'amendsCode' => $l->amendsCode(),
        'title'      => $l->getTitle(),
        'synopsis'   => $l->getSynopsis(),
        'actions'    => $actions,
        'files'      => $files
    ];
}
echo json_encode($output, JSON_PRETTY_PRINT);
