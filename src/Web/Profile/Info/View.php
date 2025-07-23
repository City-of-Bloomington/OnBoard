<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Info;

use Application\Models\Person;
use Web\People\View\View as PeopleView;

class View extends \Web\View
{
    public function __construct(Person $p)
    {
        parent::__construct();

        // Required for access control and url generation
        $_REQUEST['person_id'] = $p->getId();

        $this->vars = [
            'person' => $p,
            'applicantFiles'       => self::applicantFiles($p),
            'members'              => PeopleView::members ($p),
            'liaisons'             => PeopleView::liaisons($p),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/profile/info.twig', $this->vars);
    }

    private static function applicantFiles(Person $p): array
    {
        $canDownload = parent::isAllowed('profile', 'file_download');
        $canDelete   = parent::isAllowed('profile', 'file_delete');

        if (!$canDownload) { return []; }

        $data = [];
        foreach ($p->getFiles() as $f) {
            $links = [];
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('profile.file_delete', ['applicantFile_id'=>$f->getId()]),
                    'label' => parent::_('delete'),
                    'class' => 'delete'
                ];
            }
            $data[] = [
                'id'          => $f->getId(),
                'filename'    => $f->getFilename(),
                'updated'     => $f->getUpdated(DATE_FORMAT),
                'actionLinks' => $links
            ];
        }
        return $data;
    }
}
