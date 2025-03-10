<?php

namespace App\Livewire\Components;

use App\Models\Message;
use Livewire\Component;
use App\Models\RatingConfiguration;

class MessageRating extends Component
{
    public Message $message;
    public $rating = 0;
    public $maxScale;
    public $minScale;
    public $configurationId;


    //mount function
    public function mount(Message $message)
    {
        $this->message = $message;
        $configuration = RatingConfiguration::getActive();
        $this->maxScale = $configuration->max_scale;
        $this->minScale = $configuration->min_scale;
        $this->configurationId = $configuration->id;
        $this->rating = $message->ratings()
            ->where('user_id', auth()->id())
            ->value('rating') ?? 0;
    }

    //rate function
    public function rate($value)
    {
        $this->message->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'rating' => $value,
                'rating_configuration_id' => $this->configurationId
            ]
        );

        $this->rating = $value;
    }
    public function render()
    {
        return view('livewire.components.message-rating');
    }
}
