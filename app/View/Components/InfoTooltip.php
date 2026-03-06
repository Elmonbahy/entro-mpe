<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InfoTooltip extends Component
{
  public $message;
  public $size;

  /**
   * Create a new component instance.
   */
  public function __construct($message, $size = 'fs-5')
  {
    $this->message = $message;
    $this->size = $size;
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render(): View|Closure|string
  {
    return view('components.info-tooltip');
  }
}
