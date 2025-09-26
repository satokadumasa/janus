<?php

namespace App\Console\Commands\API\V1;

use App\Models\API\V1\Kurapital\Opportunity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class OpportunityGeoCodeSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opportunity:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'custome created command by ARS';

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
		\Log::debug("OpportunityGeoCodeSchedule::hadle() start");
        
        $opportunities = Opportunity::orderBy('id')
            ->whereBetween('receipt_date', [Carbon::now()->subDays(10)->format('Y-m-d h:i:s'), Carbon::now()->format('Y-m-d H:i:s')])
            ->whereNull('requested_lat_lng')
            ->where('status_id', 4)
            ->chunkById(100, function ($opportunities) {
                foreach ($opportunities as $opportunity) {
                    $full_address = $opportunity->prefecture . $opportunity->city . $opportunity->address;
                    if (empty($opportunity->address)) {
                        // $opportunity->address = '';
                        $address = 'no_address';

                    } else {
                        $address = $opportunity->address;
                    }
                    $opportunity->requested_lat_lng = 1;
                    $opportunity->save();
                    $getLatLong = $this->getLatLongFromOpportunityAddress($full_address);
                    $getLatLong = (Object) $getLatLong;
                    try {
                        $opportunityGeoCode = DB::table('app_opportunity_geo_codes')
                        ->updateOrInsert(
                            ['opportunity_id' => $opportunity->id],
                            [
                                'opportunity_id' => $opportunity->id,
                                'latitude' => $getLatLong->latitude,
                                'longitude' => $getLatLong->longitude,
                                'address' => $address,
                                'city' => $opportunity->city,
                                'full_address' => $full_address,
                                'prefecture' => $opportunity->prefecture,
                                'created_at' => Carbon::now()->format('Y-m-d h:i:s'),
                                'updated_at' => Carbon::now()->format('Y-m-d h:i:s'),

                            ]
                        );
                    } catch (\Throwable $th) {
                        \Log::debug("OpportunityGeoCodeSchedule::handle() ERROR:" . $th->getMessage());
                    }
                }
            });

    }

    public function getLatLongFromOpportunityAddress($address)
    {
        $appid = 'dj00aiZpPUlCS25Zd0tFSVlTVCZzPWNvbnN1bWVyc2VjcmV0Jng9MzU-';
        $place = urlencode($address); // URLエンコード
        $url = "https://map.yahooapis.jp/geocode/V1/geoCoder?appid={$appid}&query={$place}&results=1"; // XML系s機
        $this->webapi = $url;
        $res = array();
        $latitude = 0;
        $longitude = 0;

        $xml = simplexml_load_file($url);
        if (isset($xml->Error)) {
            $this->error = true;
            $this->errmsg = $xml->Message;
            $res = false;
        } else {
            if (!empty($xml->Feature->Geometry->Coordinates)) {
                $c_data = (object) $xml->Feature->Geometry->Coordinates[0];
                $cordinate = explode(",", $c_data);
                $latitude = $cordinate[1];
                $longitude = $cordinate[0];

            } else {
                $latitude = 0;
                $longitude = 0;
            }

        }
        $returnValue = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
        return $returnValue;
    }
}
