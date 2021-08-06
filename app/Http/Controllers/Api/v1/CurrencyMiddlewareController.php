<?php
/*
 * This file error code will be 10000 to 11000
 */
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Domain;
use App\Models\CurencyCash;
use App\Utils\OutputHandeling;
use App\Utils\HttpRequest;

class CurrencyMiddlewareController extends Controller
{
    /**
     * @OA\Post(
     * path="/GetRates",
     * summary="with this route you could search for the spesific rate code",
     * description="with this route you could search for the spesific rate code, you need to send minimum 3 character to get the result",
     * operationId="authRegister",
     * tags={"Currency"},
     * @OA\RequestBody(
     *    required=true,
     *    description="you need pass the token on he header and send minimum 3 leater to get a list of rate codes",
     *    @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", format="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiO"),
     *       @OA\Property(property="currency", type="string", format="string", example="dollar"),
     *    ),
     * ),
     * @OA\Response(
     *    response=10001,
     *    description="if the input validation failed then we will get this error",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="10001"),
     *       @OA\Property(property="message", type="string", example="you need to send minimum 3 charachter")
     *        ),
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="When we get minimum 3 character then a list of matched data will sent to the output",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example=""),
     *       @OA\Property(property="data", type="object", @OA\Property(property="0", type="object",
     *                                                                 @OA\Property(property="name", type="string", example="COMMUNITYDOLLARS"),
     *                                                                 @OA\Property(property="value", type="string", example="Community Dollars"),
     *                                                              ),
     *                                                     @OA\Property(property="1", type="object",
     *                                                                 @OA\Property(property="name", type="string", example="DOLA"),
     *                                                                 @OA\Property(property="value", type="string", example="Dola USD Stablecoin"),
     *                                                              ),
     *     ),
     *    ),
     *  ),
     * )
     */
    public function GetRates(Request $request, OutputHandeling $outputHandeling ){
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            'currency' => 'required| min:3']);

        if ($validator->fails()) {
            return $outputHandeling->renderErroresponse(10001, __('global.error_input_validation_error',['ERROR' => $validator->errors()->first()]),OutputHandeling::Error_code_input_validation_error );
        }

        $data = Domain::select('name','value')->where('name', 'like', '%' . $request->currency. '%' )
            ->orderBy('name')
            ->get();

        return $outputHandeling->renderDataResponse($data);
    }

    /*
     * By this route we will get all the rates name and id and we will save them inside our database to decrease the amount of call request to the Api
     */
    public function GetAndSaveRates(Request $request , OutputHandeling $outputHandeling, HttpRequest $httpRequest){
        $response = $httpRequest->SendHttpGetRequest(env('API_PROVIDER_URL'), [
                                                        'key' => env('API_PROVIDER_SECRET_KEY'),
                                                        'attributes' => 'id,name,logo_url',
                                                    ]);
        $responseBody = json_decode($response->getBody());
        if( $response->status() != 200 ){
            return $outputHandeling->renderErroresponse(10002, __('global.error_message_cannot_get_data_from_api_provider'),OutputHandeling::Error_code_external_api_call_error );
        }

        ini_set('max_execution_time', 600); //300 seconds = 5 minutes

        foreach($responseBody as $name => $data) {
            if(!Domain::select('name','value')->where('name', $data->id )->orderBy('name')->exists()){
                Domain::insert(
                    ['type' => Domain::domainType['curency'], 'name' => $data->id  ,'value' => $data->name  ,'status' => Domain::domainStatus['active'] ,'created_at'=> now()]
                );
            };
        }

        return $outputHandeling->renderSucessfullResponse(__('global.response_massage_operation_finish_successfully'));;
    }

    /**
     * @OA\Post(
     * path="/GetCurrencyInformation",
     * summary="with this route you could get the market information for a specific currency symbol",
     * description="with this route you could get the market information for a specific currency symbol, you need to send the exact symbol to this route. we will cash each currency for one hourse and if another person request the same data then we will show the data from cash system to decrease the API request call",
     * operationId="authRegister",
     * tags={"Currency"},
     * @OA\RequestBody(
     *    required=true,
     *    description="you need pass the token on he header and send the symbol name to get the market information of this symbol",
     *    @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", format="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiO"),
     *       @OA\Property(property="currency", type="string", format="string", example="COMMUNITYDOLLARS"),
     *    ),
     * ),
     * @OA\Response(
     *    response=10003,
     *    description="if the input validation failed or we can not get the valid information from the API then we will get this error",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="10003"),
     *       @OA\Property(property="message", type="string", example="We can not get the nformation from the main API")
     *        ),
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="When we can get the market information for a symbol",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example=""),
     *       @OA\Property(property="data", type="object",
     *                                                                 @OA\Property(property="id", type="string", example="DOLLAR"),
     *                                                                 @OA\Property(property="currency", type="string", example="DOLLAR"),
     *                                                                 @OA\Property(property="symbol", type="string", example="DOLLAR"),
     *                                                                 @OA\Property(property="name", type="string", example="Dollar Online"),
     *                                                                 @OA\Property(property="logo_url", type="string", example="https://s3.us-east-2.amazonaws.com/nomics-api/static/images/currencies/DOLLAR.png"),
     *                                                                 @OA\Property(property="status", type="string", example="active"),
     *                                                                 @OA\Property(property="price", type="string", example="0.43286662"),
     *                                                                 @OA\Property(property="price_date", type="string", example="2021-08-04T00:00:00Z"),
     *                                                                 @OA\Property(property="price_timestamp", type="string", example="2021-08-04T00:00:00Z"),
     *                                                                 @OA\Property(property="max_supply", type="string", example="77000000000"),
     *                                                                 @OA\Property(property="market_cap_dominance", type="string", example="0.0000"),
     *                                                                 @OA\Property(property="num_exchanges", type="string", example="3"),
     *                                                                 @OA\Property(property="num_pairs", type="string", example="5"),
     *                                                                 @OA\Property(property="num_pairs_unmapped", type="string", example="0"),
     *                                                                 @OA\Property(property="first_candle", type="string", example="2019-06-07T00:00:00Z"),
     *                                                                 @OA\Property(property="first_trade", type="string", example="2019-06-07T00:00:00Z"),
     *                                                                 @OA\Property(property="first_order_book", type="string", example="2019-06-07T00:00:00Z"),
     *                                                                 @OA\Property(property="rank", type="string", example="6854"),
     *                                                                 @OA\Property(property="rank_delta", type="string", example="-47"),
     *                                                                 @OA\Property(property="high", type="string", example="6.51074846"),
     *                                                                 @OA\Property(property="high_timestamp", type="string", example="2019-11-15T00:00:00Z"),
     *                                                                 @OA\Property(property="1d", type="object",   @OA\Property(property="volume", type="string", example="432.06"),
     *                                                                                                              @OA\Property(property="price_change", type="string", example="0.03483043"),
     *                                                                                                              @OA\Property(property="price_change_pct", type="string", example="0.0875"),
     *                                                                                                              @OA\Property(property="volume_change", type="string", example="5.94"),
     *                                                                                                              @OA\Property(property="volume_change_pct", type="string", example="0.0139"),
     *                                                                              ),
     *                                                                 @OA\Property(property="30d", type="object",   @OA\Property(property="volume", type="string", example="432.06"),
     *                                                                                                              @OA\Property(property="price_change", type="string", example="0.03483043"),
     *                                                                                                              @OA\Property(property="price_change_pct", type="string", example="0.0875"),
     *                                                                                                              @OA\Property(property="volume_change", type="string", example="5.94"),
     *                                                                                                              @OA\Property(property="volume_change_pct", type="string", example="0.0139"),
     *                                                                              ),
     *                                                              ),
     *                                                     @OA\Property(property="1", type="object",
     *                                                                 @OA\Property(property="name", type="string", example="DOLA"),
     *                                                                 @OA\Property(property="value", type="string", example="Dola USD Stablecoin"),
     *                                                              ),
     *
     *    ),
     *  ),
     * )
     */
    public function GetCurrencyInformation(Request $request , OutputHandeling $outputHandeling , HttpRequest $httpRequest){
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'currency' => 'required']);

        if ($validator->fails()) {
            return $outputHandeling->renderErroresponse(10003, __('global.error_input_validation_error',['ERROR' => $validator->errors()->first()]),OutputHandeling::Error_code_input_validation_error );
        }

        $data = CurencyCash::select()->where('name', '=', $request->currency )
                                                ->orderBy('name')
                                                ->get();

        $requestData = 0;
        if( !isset($data) || $data == null || !$data || $data->isEmpty())
            $requestData = 1;
        else {
            $startTime = Carbon::parse($data[0]->insertDate);
            $endTime = Carbon::parse(now());
            $totalDuration = $endTime->diffInHours($startTime);

            if ($totalDuration >= 1)
                $requestData = 1;
        }

        if( $requestData = 1){
            $response = $this->SaveCurrencyInformation($request->currency ,$outputHandeling ,$httpRequest);
            if( $response === null ){
                return $outputHandeling->renderErroresponse(10004, __('global.error_message_cannot_get_data_from_api_provider'),OutputHandeling::Error_code_external_api_call_error );
            }
        }
        else
            $response = json_decode($data[0]->value);


        return $outputHandeling->renderDataResponse($response);
    }


    private function SaveCurrencyInformation($currency , OutputHandeling $outputHandeling , HttpRequest $httpRequest){
        $response = $httpRequest->SendHttpGetRequest(env('API_PROVIDER_URL'), [
                                                    'key' => env('API_PROVIDER_SECRET_KEY'),
                                                    'ids' => $currency,
                                                    'interval' => '1d,30d',
                                                    'convert' => env('APP_CURRENCY'),
                                                    'per-page' => '100',
                                                    'page' => '1',
                                                ]);
        $responseBody = json_decode($response->getBody());
        if( $response->status() != 200 ){
            return null;
        }
        $deletedRows = null;
        foreach($responseBody as $name => $data) {
            $curencyCash = CurencyCash::select('name','value')->where('name', $data->id )->orderBy('name')->get();
            if(isset($curencyCash) && $curencyCash != null && !$curencyCash->isEmpty()) {
                $deletedRows = CurencyCash::where('name', $data->id)->delete();
                if(!$deletedRows)
                    return null;
            }
            CurencyCash::insert(['type' => CurencyCash::domainType['curency'], 'name' => $data->id  ,'value' =>json_encode($data) ,'insertDate'=> now()] );
        };

        return $responseBody[0];
    }

}
