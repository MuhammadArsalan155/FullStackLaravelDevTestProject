<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportProductsJob;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportController extends Controller
{
    public function import()
    {
        try {
            $path = base_path('../crawler/products.json');

            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON file not found at ' . $path
                ], 404);
            }

            $fileContents = file_get_contents($path);

            if ($fileContents === false) {
                throw new Exception('Failed to read file');
            }

            $products = json_decode($fileContents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }

            if (!$products || !is_array($products)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON structure - expected an array of products'
                ], 422);
            }

            if (empty($products)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found in JSON file'
                ], 422);
            }

            ImportProductsJob::dispatch($products);

            Log::info('Product import job dispatched', [
                'records_count' => count($products)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Import started successfully',
                'records' => count($products)
            ], 200);

        } catch (Exception $e) {
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
