<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\API\V1\Packages\JapanAddress;

class ImportJapanAddressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        JapanAddress::truncate();
        $csv_path = base_path('master') . '/ADDRESS_ALL.csv';
        $converted_csv_path = base_path('master') . '/postal_code_utf8.csv';

        file_put_contents(
            $converted_csv_path,
            mb_convert_encoding(
                file_get_contents($csv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
                'UTF-8',
                'SJIS-win'
            )
        );
        $file = new \SplFileObject($converted_csv_path);
        $file->setFlags(\SplFileObject::READ_CSV);

        if (!empty($file)) {
            foreach ($file as $row) {
                if (!empty($row) && count($row) > 6) {
                    print_r($row);
                    JapanAddress::create([
                        'zip_code' => $row[0],
                        'prefecture_ja' => $row[1],
                        'address_ja' => $row[2],
                        'street_address_ja' => $row[3],
                        'prefecture_en' => ucfirst($row[4]),
                        'address_en' => ucfirst($row[5]),
                        'street_address_en' => ucfirst($row[6]),
                        'area_group_id' => 1,//ucfirst($row[6]),

                    ]);
                } else {
                    $this->info(sprintf('Upload Success'));
                    return false;
                }

            }
        } else {
            $this->error(sprintf('There is some Error please Check your file'));
            return false;
        }
    }
}
