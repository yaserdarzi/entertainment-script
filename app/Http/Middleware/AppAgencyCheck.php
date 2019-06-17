<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Inside\Constants;
use Closure;
use Firebase\JWT\JWT;

class AppAgencyCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function handle($request, Closure $next)
    {
        $input = array_map(function ($input) {
            if (is_array($input)) {
                return array_map(array($this, 'safeTrim'), $input);
            }
            return trim($input);
        }, $request->all());
        if (!$request->header('appToken'))
            throw new ApiException(
                ApiException::EXCEPTION_UNAUTHORIZED_401,
                'Plz check your appToken header'
            );
        if (!$request->header('Authorization'))
            throw new ApiException(
                ApiException::EXCEPTION_UNAUTHORIZED_401,
                'Plz check your Authorization header'
            );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('CDN_AUTH_URL') . "/api/v1/cp/agency/app/checker",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: " . $request->header('Authorization'),
                "appName: " . Constants::APP,
                "appToken: " . $request->header('appToken')
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ($err)
            throw new ApiException(
                ApiException::EXCEPTION_UNAUTHORIZED_401,
                $err
            );
        if ($info['http_code'] != 200)
            throw new ApiException(
                ApiException::EXCEPTION_UNAUTHORIZED_401,
                json_decode($response)->error
            );
        $tokenAuth = JWT::decode($request->header('Authorization'), config("jwt.secret"), array('HS256'));
        $input['user_id'] = $tokenAuth->user_id;
        $input['agent'] = $tokenAuth->agent;
        $input['role'] = $tokenAuth->role;
        $tokenApp = JWT::decode($request->header('appToken'), config("jwt.secret"), array('HS256'));
        $input['apps_id'] = $tokenApp->apps_id;
        $input['agency_id'] = $tokenApp->agency_id;
        $input['app_id'] = json_decode($response)->data->app_id;
        $request->replace($input);
        return $next($request);
    }


    private function safeTrim($input)
    {
        if (is_array($input)) {
            return array_map(array($this, 'safeTrim'), $input);
        }
        return trim($input);
    }

}
