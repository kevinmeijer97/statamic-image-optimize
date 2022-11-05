<?php

namespace JustBetter\ImageOptimize\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Statamic\Assets\Asset;
use JustBetter\ImageOptimize\Events\ImageResizedEvent;
use JustBetter\ImageOptimize\Actions\ResizeImage;

class ResizeImageJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public Asset $asset,
        public int $width = 1680,
        public int $height = 1680,
    ) {
        $this->onConnection(config('image-optimize.default_queue_connection'));
        $this->onQueue(config('image-optimize.default_queue_name'));
    }

    public function handle(): void
    {
        new ResizeImage($this->asset, $this->width, $this->height);
        ImageResizedEvent::dispatch();
    }

    public function uniqueId(): string
    {
        return $this->asset->id();
    }
}
