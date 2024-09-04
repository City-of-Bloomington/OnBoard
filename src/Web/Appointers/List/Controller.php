<?php
declare(strict_types=1);
namespace Web\Appointers\List;

use Application\Models\AppointerTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $table = new AppointerTable();
        $appointers = $table->find();

        return new View($appointers);
    }
}