<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurencyRouteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('/api/v1/login', ['email' => 'kam.zarandy@gmail.com', 'password' => 'vihan123']);
        if($response->status() != 200) {
            print_r('route call error (test 1)');
            return $response->assertStatus(500);

        }
        $data = json_decode($response->content());
        if( (int)$data->status != 200 ) {
            print_r('User can not login (test 2)');
            return$response->assertStatus(500);
        }
        var_dump('user logined to the system');

        $token = $data->data->token;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer  '.$token,
        ])->get('/api/v1/GetCurrencyInformation?currency=DOLLAR');

        if($response->status() != 200) {
            print_r('Route error or authentication error (test 3)');
            return $response->assertStatus(500);
        }

        $data = json_decode($response->content());
        if( (int)$data->status !== 200 ) {
            print_r('Data error in output (test 4)');
            return $response->assertStatus(500);
        }
        if( !isset($data->data->id) || $data->data->id <= '' ) {
            print_r('Data Error (test 5)');
            return $response->assertStatus(500);
        }
        var_dump('Currency information get successfully');

        return $response->assertStatus(200);
    }
}
