<?php

namespace App\View\Components\Input;

use Illuminate\View\Component;

class Tomselect extends Component
{
    public $name;
    public $wire;
    public $placeholder;
    public $options;

    public function __construct($name, $wire, $placeholder, $options = [])
    {
        $this->name = $name;
        $this->wire = $wire;
        $this->placeholder = $placeholder;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.input.tomselect');
    }
}
