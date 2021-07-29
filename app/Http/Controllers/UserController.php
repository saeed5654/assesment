<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;
use Socketlabs\SocketLabsClient;

class UserController extends Controller
{
    /**
     * Handles request to login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('token')->accessToken;
            return self::withToken(true, trans('translation.login'), $token, self::SUCCESS);
        } else {
            return self::withMessage(false, trans('translation.invalid_credentials'), self::UN_AUTHORIZED);
        }
    }

    /**
     * Handles user registration request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required|string|min:4|max:20',
            'email' => 'sometimes|required|email|unique:users',
            'avatar' => 'dimensions:max_width=256,max_height=256',
            'password' => 'required|string',
            'user_role' => 'required|string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return self::withMessage(false, $errors, self::BAD_REQUEST);
        }

        try {
            if ($request->email) {
                $pin = substr(str_shuffle("0123456789"), 0, 6);
                $emailSent = $this->socketLabEmail($request->email, $pin);
                $request->pin = $pin;
                if (!(strtolower($emailSent->result) == 'success')) {
                    return self::withMessage(false, 'Error in email OTP verification', self::BAD_REQUEST);
                }
            }
            if ($request->hasFile('avatar')) {
                $fileNameToStore = $this->uploadImageToStorage($request);
                $name = explode('/', $fileNameToStore)[1];
                $request->avatar_url = '/storage/' . $name;

                //to view the image on localhost store the image with this path
                //$request->avatar_url = public_path() . '/storage/' . $name;
            }

            $user = User::store($request);

            return self::withData(true, $user, self::SUCCESS);
        } catch (\Exception $e) {
            return self::withMessage(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handles request to update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public
    function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required|string|min:4|max:20',
            'avatar' => 'dimensions:max_width=256,max_height=256',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return self::withMessage(false, $errors, self::BAD_REQUEST);
        }

        if (Auth::check()) {
            $user = User::find(auth()->user()->id);
            if (!$user) {
                return self::withMessage(false, trans('translation.not_found', ['record' => 'User']), self::NOT_FOUND);
            }

            if ($request->hasFile('avatar')) {
                $fileNameToStore = $this->uploadImageToStorage($request);
                $name = explode('/', $fileNameToStore)[1];
                $request->avatar_url = '/storage/' . $name;

                //to view the image on localhost store the image with this path
                //$request->avatar_url = public_path() . '/storage/' . $name;
            } else {
                $request->avatar_url = $user->avatar;
            }

            User::updateUser($user, $request);

            return self::withMessageAndData(true, trans('translation.record_update', ['record' => 'Profile']), $user, self::SUCCESS);
        }
        return self::withMessage(false, trans('translation.login_required'), self::UN_AUTHORIZED);
    }

    /**
     * Handles OTP verification request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request)
    {
        $user = User::find(auth()->id());
        if (!$user) {
            return self::withMessage(false, trans('translation.not_found', ['record' => 'User']), self::NOT_FOUND);
        }

        if ($user->otp_verified) {
            return self::withMessage(true, trans('translation.contact_verified', ['contact' => 'Phone']), self::SUCCESS);
        }

        User::verify($user);

        return self::withMessage(true, trans('translation.contact_verify', ['contact' => 'Email']), self::SUCCESS);
    }

    public function socketLabEmail($email, $msg)
    {
        $client = new SocketLabsClient(config("constants.SOCKET_LABS_SERVER_ID"), config("constants.SOCKET_LABS_API_KEY"));

        $message = new BasicMessage();

        $message->subject = "OTP Verification";
        $message->htmlBody = "<html>OTP: $msg</html>";
        $message->plainTextBody = "OTP:" . $msg;
        $message->from = new EmailAddress(config("constants.EMAIL_FROM"));
        $message->addToAddress($email);

        return $client->send($message);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function uploadImageToStorage($request)
    {
        $file = $request->file('avatar');
        $fileName = 'profile-' . time() . '.' . strtolower($file->getClientOriginalExtension());

        return $file->storeAs('public', $fileName);
    }
}
