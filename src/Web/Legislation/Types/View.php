<?php
/**
* @copyright 2024 City of Bloomington, Indiana
* @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
*/
namespace Web\Legislation\Types;


use Web\View as BaseView;


class View extends BaseView
{
   private $data;
   private $isUpdate;


   public function __construct($data, $isUpdate = false)
   {
       parent::__construct();
       $this->data = $data;
       $this->isUpdate = $isUpdate;
   }


   public function render(): string
   {
       $template = $this->isUpdate ? 'updateTypeForm.twig' : 'typesList.twig';
       $context = $this->isUpdate ? ['type' => $this->data] : ['types' => $this->data];
       return $this->twig->render("html/legislation/types/{$template}", $context);
   }
}


