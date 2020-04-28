<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
/*
set FOREIGN_KEY_CHECKS=0;
truncate table legislationFiles;
truncate table legislationActions;
truncate table legislation_tags;
truncate table legislation;
truncate table legislationStatuses;
truncate table tags;
set FOREIGN_KEY_CHECKS=1;
*/
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\Models\Committee;
use Application\Models\Tag;
use Web\Database;

$_SERVER['SITE_HOME'] = '/srv/data/onboard';
include '../../bootstrap.php';

$CSV   = fopen('legislation.csv', 'r');
$files = '/srv/data/legislation/files';

define('YEAR',              0);
define('TYPE',              1);
define('NUMBER',            2);
define('TITLE',             3);
define('TAGS',              4);
define('AMENDS',            5);
define('NOTES',             6);
define('COMMITTEE_DATE',    7);
define('COMMITTEE_VOTE',    8);
define('COMMITTEE_OUTCOME', 9);
define('FINAL_DATE',       10);
define('FINAL_VOTE',       11);
define('FINAL_OUTCOME',    12);
define('SYNOPSIS',         13);

$types = [
    'Ordinance'               => ['id' => 1, 'abbr'=>'Ord'         ],
    'Resolution'              => ['id' => 2, 'abbr'=>'Res'         ],
    'Appropriation Ordinance' => ['id' => 3, 'abbr'=>'App Ord'     ],
    'Bond Ordinance'          => ['id' => 5, 'abbr'=>'Bond Ord'    ],
    'Special Appropriation Ordinance' => ['id' => 6, 'abbr'=>'Spec App Ord']
];

$subtypes = [
    'Amendment'            => ['id' => 4],
    'Attachment'           => ['id' => 7],
    'Divided Question'     => ['id' => 8],
    'Reasonable Condition' => ['id' => 9]
];


$COMMITTEE = new Committee(1);

$COMMITTEE_ACTION = new ActionType('Committee');
$FINAL_ACTION     = new ActionType('Final');

$TABLE = new LegislationTable();

while (($line = fgetcsv($CSV)) !== false) {
    echo "Parsing ".$line[TYPE].' '.$line[YEAR].' '.$line[NUMBER]."\n";

    $amendsCode = ($line[AMENDS] == 'Yes') ? 1 : 0;

    $type = new Type($line[TYPE]);

    try {
        $status = new Status($line[FINAL_OUTCOME]);
    }
    catch (\Exception $e) {
        $status = new Status();
        $status->setName($line[FINAL_OUTCOME]);
        $status->save();
    }

    $tags = [];
    foreach (explode('|', $line[TAGS]) as $t) {
        $t = trim($t);
        if ($t) {
            try { $tag = new Tag($t); }
            catch (\Exception $e) {
                $tag = new Tag();
                $tag->setName($t);
                $tag->save();
            }
            $tags[] = $tag->getId();
        }
    }

    $l = new Legislation();
    $l->setCommittee($COMMITTEE);
    $l->setTitle     ($line[TITLE   ]);
    $l->setYear      ($line[YEAR    ]);
    $l->setNumber    ($line[NUMBER  ]);
    $l->setSynopsis  ($line[SYNOPSIS]);
    $l->setNotes     ($line[NOTES   ]);
    $l->setType      ($type);
    $l->setStatus    ($status);
    $l->setAmendsCode($amendsCode);

    if ($type->isSubtype()) {
        $l->setParent_id($parent_id);
    }

    $l->save();
    if (count($tags)) { $l->saveTags($tags); }

    if (!$type->isSubtype()) {
        $parent_id = $l->getId();
    }

    if ($line[COMMITTEE_DATE] && $line[COMMITTEE_DATE] != 'n/a') {
        $action = new Action();
        $action->setLegislation($l);
        $action->setType($COMMITTEE_ACTION);
        $action->setActionDate($line[COMMITTEE_DATE   ]);
        $action->setVote      ($line[COMMITTEE_VOTE   ]);
        $action->setOutcome   ($line[COMMITTEE_OUTCOME]);
        $action->save();
    }

    if ($line[FINAL_DATE] && $line[FINAL_DATE] != 'n/a') {
        $action = new Action();
        $action->setLegislation($l);
        $action->setType($FINAL_ACTION);
        $action->setActionDate($line[FINAL_DATE   ]);
        $action->setVote      ($line[FINAL_VOTE   ]);
        $action->setOutcome   ($line[FINAL_OUTCOME]);
        $action->save();
    }

    if (!$type->isSubtype()) {
        $abbr     = $types[$type->getName()]['abbr'];
        $tempFile = "$files/{$l->getYear()}/$abbr {$l->getNumber()}.pdf";
        if (is_file($tempFile)) {
            $lf = new LegislationFile();
            $lf->setLegislation($l);
            $lf->setFile($tempFile);
            $lf->save();
        }
        else {
            echo "missing $tempFile\n";
        }
    }
}
