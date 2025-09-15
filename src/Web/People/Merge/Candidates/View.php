<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Merge\Candidates;

class View extends \Web\View
{
    public function __construct(array $people, array $search, array $duplicates)
    {
        parent::__construct();

        $this->vars = [
            'people'      => $people,
            'duplicates'  => $duplicates,
            'firstname'   => $search['firstname'] ?? '',
            'lastname'    => $search['lastname' ] ?? '',
            'email'       => $search['email'    ] ?? '',
            'breadcrumbs' => self::breadcrumbs()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/merge/candidates.twig', $this->vars);
    }

    public static function breadcrumbs(): array
    {
        return [
                parent::_('settings') => parent::generateUri('settings.index'),
                parent::_('merge_people') => parent::generateUri('people.merge.index')
        ];
    }
}
