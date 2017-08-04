<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
/*
truncate table legislationFiles;
truncate table legislationActions;
truncate table legislation_tags;
delete from legislation;
*/
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\Models\Committee;
use Blossom\Classes\Database;

include '../../bootstrap.inc';

define('TYPE',           0);
define('NUMBER',         1);
define('TITLE',          2);
define('TAGS',           3);
define('AMENDS',         4);
define('COMMITTEE_DATE', 5);
define('COMMITTEE_VOTE', 6);
define('FINAL_DATE',     7);
define('FINAL_VOTE',     8);
define('SYNOPSIS',       9);


$COMMITTEE = new Committee(1);
$TYPE      = new Type('Ordinance');
$AMENDMENT = new Type('Amendment');

$COMMITTEE_ACTION = new ActionType('Committee');
$FINAL_ACTION     = new ActionType('Final');

$TABLE = new LegislationTable();

$CSV  = fopen('2017.csv', 'r');
while (($line = fgetcsv($CSV)) !== false) {
    $amendsCode = ($line[AMENDS] == 'Yes') ? 1 : 0;


    $l = new Legislation();
    $l->setCommittee($COMMITTEE);
    $l->setTitle     ($line[TITLE]);
    $l->setAmendsCode($amendsCode);
    $l->setSynopsis  ($line[SYNOPSIS]);
    $l->setType_id   ($line[TYPE]);

    if ($line[NUMBER]) {
        $l->setNumber($line[NUMBER]);
    }
    else {
        $amendments = $TABLE->find(['parent_id'=>$parent_id]);
        $l->setNumber(count($amendments) + 1);
        $l->setType($AMENDMENT);
        $l->setParent_id($parent_id);
    }

    echo "Saving {$l->getNumber()}:\n";
    $l->save();
    if ($l->getType()->getName() != 'Amendment') {
        $parent_id = $l->getId();
    }

    if ($line[COMMITTEE_DATE] && $line[COMMITTEE_DATE] != 'n/a') {
        $action = new Action();
        $action->setLegislation($l);
        $action->setType($COMMITTEE_ACTION);
        $action->setActionDate($line[COMMITTEE_DATE]);
        $action->setVote      ($line[COMMITTEE_VOTE]);
        $action->save();
    }

    if ($line[FINAL_DATE] && $line[FINAL_DATE] != 'n/a') {
        $action = new Action();
        $action->setLegislation($l);
        $action->setType($FINAL_ACTION);
        $action->setActionDate($line[FINAL_DATE]);
        $action->setVote      ($line[FINAL_VOTE]);
        $action->save();
    }

}
