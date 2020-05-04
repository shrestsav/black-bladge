<?php

namespace App\Http\Controllers\Api;

use App\AppDefault;
use App\Category;
use App\Http\Controllers\Controller;
use App\MainArea;
use App\Offer;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Resources\Api\AppDefault as AppDefaultResource;

class CoreController extends Controller
{
    public function appDefaults()
    {
        $appConfigs = AppDefault::firstOrFail();
        
        return new AppDefaultResource($appConfigs);
    }

    public function getSettings($settingType)
    {
        if($settingType!=''){
            $settings = config('settings.'.$settingType);
            return response()->json($settings);
        }
        else{
            return response()->json([
                'status' => 403,
                'message'=> 'Setting type required'
            ],403);
        }
    }

    public function supportInfo()
    {
        $appDefaults = AppDefault::firstOrFail();
        $appDefaults['company_logo'] = asset('files/'.$appDefaults->company_logo);

        $input = $appDefaults->only('company_logo', 'company_email', 'hotline_contact', 'FAQ_link', 'online_chat');

        return response()->json($input);
    }    

    public function termsAndConditions()
    {
        $TACS = AppDefault::firstOrFail()->TACS;
        
        return response()->json($TACS);
    }

    public function FAQS()
    {
        $FAQS = AppDefault::firstOrFail()->FAQS;
        
        return response()->json($FAQS);
    }
}
