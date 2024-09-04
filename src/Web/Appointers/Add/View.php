<?php
declare(strict_types=1);
namespace Web\Appointers\Add;

class View extends \Web\View
{
    private $appointer;

    public function __construct($appointer)
    {
        parent::__construct();
        $this->appointer = $appointer;
        $this->vars['appointer'] = $this->appointer;
    }

    public function render(): string
    {
        return $this->twig->render('html/appointers/add.twig', $this->vars);
    }
}