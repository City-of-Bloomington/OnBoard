<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\List;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(array $files, Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'files'       => self::filedata($files),
            'committee'   => $committee,
            'actionLinks' => self::actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/files/list.twig', $this->vars);
    }

    private static function actionLinks(Committee $c): array
    {
        $links = [];
        if (parent::isAllowed('committees.files', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('committees.files.add', ['committee_id'=>$c->getId()]),
                'label' => parent::_('add'),
                'class' => 'add'
            ];
        }
        return $links;
    }

    private static function filedata(array $files): array
    {
        $data    = [];
        $canEdit = parent::isAllowed('committees.files', 'update');
        $canDel  = parent::isAllowed('committees.files', 'delete');
        foreach ($files as $f) {
            $params = [
                'file_id'      => $f->getId(),
                'committee_id' => $f->getCommittee_id()
            ];
            $d = [
                'id'          => $f->getId(),
                'type'        => $f->getType(),
                'url'         => $f->getUrl() ?: parent::generateUri('committees.files.download', $params),
                'filename'    => $f->getFilename(),
                'title'       => $f->getTitle(),
                'committee'   => $f->getCommittee()->getName()
            ];

            if ($canEdit) {
                $d['actions'][] = [
                    'url'   => parent::generateUri('committees.files.update', $params),
                    'label' => parent::_('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDel) {
                $d['actions'][] = [
                    'url'   => parent::generateUri('committees.files.delete', $params),
                    'label' => parent::_('delete'),
                    'class' => 'delete'
                ];
            }
            $data[] = $d;
        }
        return $data;
    }
}
