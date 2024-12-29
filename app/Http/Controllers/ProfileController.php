<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\User;
use Illuminate\Http\Request;



class ProfileController extends Controller
{

    public function showProfile(Request $request)
    {
        // الحصول على المستخدم بناءً على التوكن المرسل
        $currentUser = $request->user();

        // التحقق إذا كان المستخدم هو من نوع User أو Expert بناءً على guard
        if ($currentUser instanceof \App\Models\User) {
            // إذا كان المستخدم هو من نوع User
            $profileData = [
                'type' => 'User',
                'userName' => $currentUser->userName,
                'email' => $currentUser->email,
                'imagePath' => $currentUser->imagePath,
                'timezone' => $currentUser->timezone,
                'mobile' => $currentUser->mobile
            ];
        } elseif ($currentUser instanceof \App\Models\Expert) {
            $schedules = $currentUser->schedules; // fetch schedule time

            $scheduleData = $schedules->map(function($schedule) {
                return [
                    'day' => $schedule->day,
                    'start_time' => $schedule->start,
                    'end_time' => $schedule->end
                ];
            });

            $profileData = [
                'type' => 'Expert',
                'userName' => $currentUser->userName,
                'email' => $currentUser->email,
                'mobile' => $currentUser->mobile,
                'imagePath' => $currentUser->imagePath,
                'timezone' => $currentUser->timezone,
                'category' => $currentUser->category->categoryName,
                'section' => $currentUser->section->sectionName,
                'experience' => $currentUser->experience,
                'rate' => $currentUser->rate,
                'start_time' => $currentUser->start_time,
                'end_time' => $currentUser->end_time,
                'schedules' => $scheduleData
            ];
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($profileData);
    }




    public function updateProfile(Request $request){


    }


    public function showOtherProfile($userName){


    }


}
