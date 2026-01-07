<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $products;
    public int $tries = 3;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting product import', ['count' => count($this->products)]);

        DB::beginTransaction();

        try {
            foreach ($this->products as $index => $item) {
                if (!isset($item['title']) || !isset($item['price'])) {
                    Log::warning("Skipping product at index {$index}: missing required fields", $item);
                    continue;
                }

                $product = Product::updateOrCreate(
                    ['title' => $item['title']],
                    [
                        'price' => $item['price'],
                        'description' => $item['description'] ?? null,
                        'category' => $item['category'] ?? null,
                    ]
                );

                if (!empty($item['image'])) {
                    $urls = is_array($item['image']) ? $item['image'] : [$item['image']];

                    foreach ($urls as $url) {

                        if (str_starts_with($url, 'data:image')) {
                            Log::debug("Skipping base64 placeholder image for product: {$product->title}");
                            continue;
                        }
                        $product->images()->firstOrCreate(
                            ['url' => $url],
                            ['product_id' => $product->id]
                        );
                    }
                }

                Log::debug("Product processed: {$product->title}", [
                    'id' => $product->id,
                    'images_count' => $product->images()->count()
                ]);
            }

            DB::commit();

            Log::info('Product import completed successfully', [
                'total_processed' => count($this->products)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Product import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
    public function failed(\Throwable $exception): void
    {
        Log::error('ImportProductsJob failed permanently', [
            'error' => $exception->getMessage(),
            'products_count' => count($this->products)
        ]);
    }
}
