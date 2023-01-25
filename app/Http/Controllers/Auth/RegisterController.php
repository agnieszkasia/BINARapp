<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'companyName' => ['required', 'string', 'max:255', 'min:2'],
            'firstName' => ['required','string', 'max:255', 'min:2'],
            'lastName' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'birthDate' => ['required', 'string','regex:/19[0-9]{2}|200[0,1,2,3]-[0-9]{2}-[0-9]{2}/u', 'max:255'],
            'phoneNumber' => ['required', 'string','regex:/^[0-9]{9}/u', 'size:9'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'taxOfficeCode' => ['required', 'string'],
        ]);
    }

    /**
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
//        return User::create([
//            'email' => $data['email'],
//            'password' => Hash::make($data['password']),
//        ]);

        UserService::createNewUser($data);

    }
}
