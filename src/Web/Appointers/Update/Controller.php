<?php
declare(strict_types=1);
namespace Web\Appointers\Update;

use Application\Models\Appointer;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $appointer = null;
        if (!empty($_REQUEST['appointer_id'])) {
            try {
                $appointer = new Appointer($_REQUEST['appointer_id']);
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        } else {
            $appointer = new Appointer();
        }

        if (isset($_POST['name'])) {
            $appointer->setName($_POST['name']);
            try {
                $appointer->save();
                $return_url = \Web\View::generateUrl('appointers.index');
                header("Location: $return_url");
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        return new View($appointer);
    }
}