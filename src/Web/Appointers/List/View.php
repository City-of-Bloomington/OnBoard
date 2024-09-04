<?php
declare(strict_types=1);
namespace Web\Appointers\List;

class View extends \Web\View
{
    private $appointers;

    public function __construct($appointers)
    {
        parent::__construct();
        $this->appointers = $appointers;
        $this->vars['appointers'] = $this->appointers;
    }

    public function render(): string
    {
        return $this->twig->render('html/appointers/list.twig', $this->vars);
    }
}