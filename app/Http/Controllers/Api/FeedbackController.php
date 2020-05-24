<?php

namespace App\Http\Controllers\Api;

use Auth;
use Validator;
use App\UserFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feedback' => 'required|min:1max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $feedback = UserFeedback::create([
            'user_id'  => Auth::id(),
            'feedback' => $request->feedback
        ]);

        return response()->json($feedback);
    }
}
