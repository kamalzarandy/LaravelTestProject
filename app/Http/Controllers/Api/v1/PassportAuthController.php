<?php
/*
 * the error code for this file will be 11000 to 12000
 */
namespace App\Http\Controllers\Api\v1;

use App\Utils\OutputHandeling;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


use Illuminate\Support\Facades\Validator;



class PassportAuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/register",
     * summary="This route will register a new user in the application, anonymouse user can register on the platform",
     * description="This route will register a new user in the application, anonymouse user can register on the platform. the user will insert the information if user was not exist in the platform the user will registered and a token will generrate and will send to the output. this token must used in all the request",
     * operationId="authRegister",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="pass the user information",
     *    @OA\JsonContent(
     *       required={"name","email", "password", "confirmed_password"},
     *       @OA\Property(property="name", type="string", format="string", example="kamal zarandi"),
     *       @OA\Property(property="email", type="string", format="email", example="kam.zarandy@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=11000,
     *    description="When we face with the validation error or the name or email exist in the system then this message will show to the users",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="11000"),
     *       @OA\Property(property="message", type="string", example="Sorry, validatio error")
     *        ),
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="when User registered successfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example="user created sucessfully"),
     *       @OA\Property(property="data", type="object", @OA\Property(property="user", type="object",
     *                                                                 @OA\Property(property="email", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="name", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="updated_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="created_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="id", type="integer", example="user created sucessfully"),
     *                                                                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                                                              ),
     *                                                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDA5MzI0Ny05M2EwLTQxMmYtYjE0ZS0yN2QwNDRjYjAzMGUiLCJqdGkiOiJhZDg2NmQ1OTg5MDY0YjQ1NDFhNzA4ZWE4NGM4OGU4MzE0N2JiZTNlMTE5ZWE5YTc3ZTZjODY0M2UwNWEwNTYxNTAyOTFkY2IzMWRkOGNkYSIsImlhdCI6MTYyODEwMzk4OSwibmJmIjoxNjI4MTAzOTg5LCJl"),
     *                                                                 ),
     *     ),
     *    ),
     *  ),
     * )
     */

    public function register(Request $request, OutputHandeling $outputHandeling)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55|min:5|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return $outputHandeling->renderErroresponse(11000, __('global.error_input_validation_error',['ERROR' => $validator->errors()->first()]),OutputHandeling::Error_code_input_validation_error );
        }

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $accessToken = $user->createToken('UserToken')->accessToken;

        return $outputHandeling->renderDataResponse([ 'user' => $user,
                                                    'token' => $accessToken,
                                                    'token_type' => 'Bearer']);

    }

    /**
     * @OA\Post(
     * path="/login",
     * summary="With this route user can login to the system",
     * description="With this route user can login to the system",
     * operationId="authRegister",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="pass the user email and password",
     *    @OA\JsonContent(
     *       required={"email", "password"},
     *       @OA\Property(property="email", type="string", format="email", example="kam.zarandy@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=11001,
     *    description="if the input validation fales or if the user or password not exist then this message will send to the output",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="11001"),
     *       @OA\Property(property="message", type="string", example="Email or password is wrong")
     *        ),
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="when User login successfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example="user created sucessfully"),
     *       @OA\Property(property="data", type="object", @OA\Property(property="user", type="object",
     *                                                                 @OA\Property(property="email", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="name", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="updated_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="created_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="id", type="integer", example="user created sucessfully"),
     *                                                                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                                                              ),
     *                                                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDA5MzI0Ny05M2EwLTQxMmYtYjE0ZS0yN2QwNDRjYjAzMGUiLCJqdGkiOiJhZDg2NmQ1OTg5MDY0YjQ1NDFhNzA4ZWE4NGM4OGU4MzE0N2JiZTNlMTE5ZWE5YTc3ZTZjODY0M2UwNWEwNTYxNTAyOTFkY2IzMWRkOGNkYSIsImlhdCI6MTYyODEwMzk4OSwibmJmIjoxNjI4MTAzOTg5LCJl"),
     *                                                                 ),
     *     ),
     *    ),
     *  ),
     * )
     */
    public function login(Request $request, OutputHandeling $outputHandeling)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $outputHandeling->renderErroresponse(11001, __('global.error_input_validation_error',['ERROR' => $validator->errors()->first()]),OutputHandeling::Error_code_input_validation_error );
        }

        if (!auth()->attempt($data)) {
            return $outputHandeling->renderErroresponse(11001, __('global.error_email_or_password_is_wrong'),OutputHandeling::Error_code_data_error );
        }

        $user = auth()->user();
        $tokenResult = $user->createToken('userToken');
        $tokenModel = $tokenResult->token;
        if ($request->remember_me)
            $tokenModel->expires_at = Carbon::now()->addWeeks(1);
        $tokenModel->save();

        return $outputHandeling->renderDataResponse([ 'user' => $user,
                                                    'token' => $tokenResult->accessToken,
                                                    'token_type' => 'Bearer']);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * summary="With this route user will log out from system",
     * description="With this route user will log out from system",
     * operationId="authRegister",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="pass the token on the header",
     *    @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", format="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiO"),
     *    ),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="when Logout sucessfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example="Logout sucessfully"),
     *     ),
     *    ),
     *  ),
     * )
     */
    public function logout(Request $request, OutputHandeling $outputHandeling)
    {
        /** @var User $user
         */
        $request->user()->token()->revoke();
        return $outputHandeling->renderSucessfullResponse(__('global.Log_out_successful'));;
    }

    /**
     * @OA\Post(
     * path="/userInfo",
     * summary="With this route the user information will sent to the output",
     * description="With this route the user information will sent to the output",
     * operationId="authRegister",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="pass the token on the header",
     *    @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", format="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOeyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiO"),
     *    ),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="when the token will be valid, if it will not valid then you will get 500 Error code",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example="200"),
     *       @OA\Property(property="message", type="string", example="user created sucessfully"),
     *       @OA\Property(property="data", type="object", @OA\Property(property="user", type="object",
     *                                                                 @OA\Property(property="email", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="name", type="string", example="user created sucessfully"),
     *                                                                 @OA\Property(property="updated_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="created_at", type="datetime", example="user created sucessfully"),
     *                                                                 @OA\Property(property="id", type="integer", example="user created sucessfully"),
     *                                                                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                                                              ),
     *                                                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NDA5MzI0Ny05M2EwLTQxMmYtYjE0ZS0yN2QwNDRjYjAzMGUiLCJqdGkiOiJhZDg2NmQ1OTg5MDY0YjQ1NDFhNzA4ZWE4NGM4OGU4MzE0N2JiZTNlMTE5ZWE5YTc3ZTZjODY0M2UwNWEwNTYxNTAyOTFkY2IzMWRkOGNkYSIsImlhdCI6MTYyODEwMzk4OSwibmJmIjoxNjI4MTAzOTg5LCJl"),
     *                                                                 ),
     *     ),
     *    ),
     *  ),
     * )
     */
    public function userInfo(OutputHandeling $outputHandeling)
    {
        $user = auth()->user();
        return $outputHandeling->renderDataResponse(['user' => $user]);
    }
}
