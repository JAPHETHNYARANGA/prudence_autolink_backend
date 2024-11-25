<?php

namespace App\Jobs;

use App\Services\CarListingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $carId;

    public function __construct($carId)
    {
        $this->carId = $carId;
    }

    public function handle(CarListingService $carListingService)
    {
        $carListingService->deleteCar($this->carId);
    }
}
