<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\Add;

use Application\Models\Committee;
use Application\Models\CommitteeFile;
use Web\Committees\Files\Update\Controller as UpdateController;
use Web\Committees\Files\Update\View       as UpdateView;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try { $c = new Committee($params['committee_id']); }
        catch (\Exception $e) { return new \Web\Views\NotFoundView();}

        $file = new CommitteeFile();
        $file->setCommittee($c);

        if (isset($_POST['type'])) {
            try { UpdateController::saveAndRedirect($file); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new UpdateView($file);
    }
}
