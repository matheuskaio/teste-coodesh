<?php

namespace App\Console\Commands;

use App\Models\ImportHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SyncErrorNotification;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImportOpenFoodProducts extends Command
{
    protected $signature = 'openfood:import';
    protected $description = 'Importa todos os arquivos delta do Open Food Facts (máx. 100 produtos por arquivo)';

    public function handle(): void
    {
        $this->info('Iniciando importação de múltiplos arquivos...');

        try {
            $indexUrl = 'https://static.openfoodfacts.org/data/delta/index.txt';
            $response = Http::get($indexUrl);

            if (!$response->ok()) {
                throw new \Exception('Erro ao acessar index.txt');
            }

            $files = explode("\n", trim($response->body()));
            $totalImportados = 0;

            foreach ($files as $filename) {

                if (ImportHistory::where('filename', $filename)->exists()) {
                    $this->warn("Arquivo já importado, pulando: $filename");
                    continue;
                }

                if (empty($filename)) continue;

                $this->info("Importando arquivo: $filename");

                $gzUrl = "https://static.openfoodfacts.org/data/delta/{$filename}";
                $gzPath = storage_path("app/temp/{$filename}");

                if (!file_exists(dirname($gzPath))) {
                    mkdir(dirname($gzPath), 0777, true);
                }

                file_put_contents($gzPath, file_get_contents($gzUrl));

                $handle = gzopen($gzPath, 'rb');
                if (!$handle) {
                    $this->warn("Não foi possível abrir {$filename}");
                    continue;
                }

                $count = 0;
                while (!gzeof($handle) && $count < 100) {
                    $line = gzgets($handle);
                    $data = json_decode($line, true);

                    if (!$data || empty($data['code'])) continue;

                    try {
                        Product::updateOrCreate(
                            ['code' => $data['code']],
                            [
                                'product_name'      => $data['product_name'] ?? null,
                                'brands'            => $data['brands'] ?? null,
                                'categories'        => $data['categories'] ?? null,
                                'image_url'         => $data['image_url'] ?? null,
                                'quantity'          => $data['quantity'] ?? null,
                                'labels'            => $data['labels'] ?? null,
                                'cities'            => $data['cities'] ?? null,
                                'purchase_places'   => $data['purchase_places'] ?? null,
                                'stores'            => $data['stores'] ?? null,
                                'ingredients_text'  => $data['ingredients_text'] ?? null,
                                'traces'            => $data['traces'] ?? null,
                                'serving_size'      => $data['serving_size'] ?? null,
                                'serving_quantity'  => $data['serving_quantity'] ?? null,
                                'nutriscore_score'  => $data['nutriscore_score'] ?? null,
                                'nutriscore_grade'  => $data['nutriscore_grade'] ?? null,
                                'main_category'     => $data['main_category'] ?? null,
                                'imported_t'        => now(),
                                'status'            => 'draft',
                                'url'               => $data['url'] ?? null,
                                'creator'           => $data['creator'] ?? null,
                                'created_t'         => isset($data['created_t']) ? Carbon::createFromTimestamp($data['created_t']) : null,
                                'last_modified_t'   => isset($data['last_modified_t']) ? Carbon::createFromTimestamp($data['last_modified_t']) : null,
                            ]
                        );

                        $count++;
                        $totalImportados++;
                    } catch (\Throwable $e) {
                        logger()->warning("Erro produto {$data['code']} - " . $e->getMessage());
                    }
                }

                gzclose($handle);
                unlink($gzPath);

                $this->info("→ {$count} produtos importados de {$filename}");
                ImportHistory::create([
                    'filename' => $filename,
                    'imported_count' => $count,
                    'imported_at' => now(),
                ]);
            }

            $this->info("Importação finalizada. Total de produtos importados: {$totalImportados}");
            Log::info('[CRON] openfood:import executado com sucesso às ' . now());
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            Notification::route('mail', 'admin@empresa.com')
                ->notify(new SyncErrorNotification($errorMessage));

            logger()->error('Erro no Sync: ' . $errorMessage);
            $this->error('Erro na sincronização. Notificação enviada.');
        }
    }
}
