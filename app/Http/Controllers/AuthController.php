<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expert;
use App\Models\User;
use App\Models\Section;
use App\Models\ExpertSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register Function
    public function register()
    {
        if (request('isExpert')) {
            return $this->registerExpert();
        } else {
            return $this->registerClient();
        }
    }

    // Expert Registration
    public function registerExpert()
    {
        $validated = $this->validateExpertRegistration();

        // Creating expert instance and assigning its password
        $expert = Expert::create($validated);
        $expert->password = Hash::make($validated['password']);
        $expert->save();

        // Getting the selected working days and working hours
        $workingDays = request('working_days'); // Array of selected days
        $start_time = request('start_time');
        $end_time = request('end_time');

        // Add schedule for each selected day
        foreach ($workingDays as $day) {
            $expert->schedules()->create([
                'day' => $day,
                'start' => $start_time,
                'end' => $end_time,
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Expert registration successful',
        ]);
    }

    // Client Registration
    public function registerClient()
    {
        $validated = $this->validateClientRegistration();

        $user = User::create($validated);
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Creating wallet for client
        //$user->wallet()->create();

        return response()->json([
            'status' => 1,
            'message' => 'Client registered successfully',
        ]);
    }

    // Login Function
    public function login()
    {
        \Log::info('isExpert value: ' . request('isExpert')); // تتبع قيمة isExpert

        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if (request('isExpert')) {
            return $this->loginExpert($credentials);
        } else {
            return $this->loginClient($credentials);
        }
    }

    // Expert Login
    public function loginExpert($credentials)
    {
        \Log::info('Attempting expert login with: ' . json_encode($credentials)); // تتبع بيانات تسجيل
        if (!Auth::guard('experts')->attempt($credentials)) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized',
            ]);
        }

        $user = Auth::guard('experts')->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Expert login successful',
            'isExpert' => 1,
            'token' => $token,
        ]);

    }

    // Client Login
    public function loginClient($credentials)
    {
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid credentials',
            ]);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Client login successful',
            'isExpert' => 0,
            'access_token' => $token,
        ]);
    }

    public function logout(){
        if (Auth::user() instanceof App\Models\Expert){
            return $this->logoutExpert();
        }
        else{
            return $this->logoutClient();
        }
    }

    // Logout Expert
    public function logoutExpert()
    {
        $expert = request()->user('experts');
        $expert->tokens()->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Logged out successfully',
        ]);
    }

    // Logout Client
    public function logoutClient()
    {
        $client = request()->user();
        $client->tokens()->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Logged out successfully',
        ]);
    }

    // Validation for Expert Registration
    public function validateExpertRegistration()
    {
        return request()->validate([
            'userName' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users', 'unique:experts'],
            'mobile' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'timezone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'category_id' => ['required', 'exists:categories,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'experience' => ['nullable', 'string', 'max:500'],
            'working_days' => ['required', 'array'], // Array of days
            'working_days.*' => ['required', 'string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }

    // Validation for Client Registration
    public function validateClientRegistration()
    {
        return request()->validate([
            'userName' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users', 'unique:experts'],
            'mobile' => ['string', 'max:13'],
            'timezone' => ['required', 'string', 'timezone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

}
