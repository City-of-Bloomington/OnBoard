<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Application;
use Application\Models\Committee;
use Application\Models\Notifications\Definition;
use Application\Models\Notifications\DefinitionTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                if (!$committee->takesApplications()) {
                    unset($committee);
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($committee)) { return new \Web\Views\NotFoundView(); }

        $application = new Application();
        $application->setCommittee($committee);
        $application->setPerson($_SESSION['USER']);

        if (isset($_POST['interest'])) {
            try {
                $application->handleUpdate($_POST);
                self::notify($application);
                $application->save();
                return new Success($application);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($_POST ?? [], $committee);
    }

    private static function notify(Application $a)
    {
        $n = self::definition(__NAMESPACE__.'::confirmation', $a);
        if (isset($n)) { $n->send([$a->getPerson()], $a); }

        $p = $a->getPeopleToNotify();
        $n = self::definition(__NAMESPACE__.'::notice', $a);
        if (isset($n) && count($p)) {
            $n->send($p, $a);
        }
    }

    private static function definition(string $event, Application $a): ?Definition
    {
        $t = new DefinitionTable();
        $l = $t->find(['committee_id'=>$a->getCommittee_id(), 'event'=>$event]);
        if (count($l)) { return $l->current(); }
        else {
            $l = $t->find(['committee_id'=>null, 'event'=>$event]);
            if (count($l)) { return $l->current(); }
        }
        return null;
    }
}
