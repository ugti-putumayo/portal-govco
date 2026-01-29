namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $title;
    public $content;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.card');
    }
}

<div class="card mb-3">
    <div class="card-header">
        {{ $title }}
    </div>
    <div class="card-body">
        {{ $content }}
    </div>
</div>