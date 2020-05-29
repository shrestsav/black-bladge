<?php

namespace App\Http\Controllers;

use App\AppDefault;
use App\Category;
use App\Item;
use App\User;
use App\MainArea;
use App\PushNotification;
use App\Offer;
use App\Service;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Illuminate\Support\Str;
use App\Http\Resources\Api\Offer as OfferResource;

class CoreController extends Controller
{
    public function getFields($fieldType)
    {
    	if($fieldType!=''){
	    	$fields = config('fields.'.$fieldType);
	    	return $fields;
	    }
	    else{
	    	return json_encode('Field Type Required');
	    }
    }
    public function getSettings($settingType)
    {
    	if($settingType!=''){
	    	$settings = config('settings.'.$settingType);
	    	return $settings;
	    }
	    else{
	    	return json_encode('Setting Type Required');
	    }
    }

    public function appDefaults()
    {
        $appDefaults = AppDefault::first();
        $appDefaults['company_logo_url'] = asset('files/'.$appDefaults->company_logo);        
        return response()->json($appDefaults);
    }
    
    public function updateAppDefaults(Request $request)
    {
        $input = [];
        if($request->saveType=='generalSetting'){
            $validatedData = $request->validate([
                'VAT'           => 'required|numeric',
                'OTP_expiry'    => 'required|numeric',
                'app_rows'      => 'required|numeric',
                'sys_rows'      => 'required|numeric',
                'cost_per_km'   => 'required|numeric',
                'cost_per_min'  => 'required|numeric',
            ]);

            $input = $request->only('VAT', 'OTP_expiry','app_rows','sys_rows','cost_per_km','cost_per_min');
        }
        if($request->saveType=='supportSetting'){
            $validatedData = $request->validate([
                'logoFile' => 'mimes:jpeg,png,jpg|max:6144',
                'company_logo' => 'required|string|max:255',
                'company_email' => 'required|string|email|max:255',
                'hotline_contact' => 'required|string|max:255',
                'FAQ_link' => 'required|string|max:255',
                'online_chat' => 'required',
            ]);

            $input = $request->only('company_logo', 'company_email', 'hotline_contact', 'FAQ_link', 'online_chat');
            $input['online_chat'] = json_decode($request->online_chat,true);

            if ($request->hasFile('logoFile')) {
                $validator = Validator::make($request->all(), [
                    "logoFile" => 'mimes:jpeg,bmp,png|max:3072',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => '422',
                        'message' => 'Validation Failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
                $photo = $request->file('logoFile');
                $fileName = 'company_logo.'.$photo->getClientOriginalExtension();
                $uploadDirectory = public_path('files');
                $photo->move($uploadDirectory, $fileName);

                $input['company_logo'] = $fileName;
            }
        }
        if($request->saveType=='orderSetting'){
            $input = $request->only('order_time', 'driver_notes');
        }
        if($request->saveType=='TACS'){
            $input = $request->only('TACS');
        }
        if($request->saveType=='FAQS'){
            $input = $request->only('FAQS');
        }
        if($request->saveType=='copyrights'){
            $input = $request->only('copyrights');
        }
        if($request->saveType=='privacy_policies'){
            $input = $request->only('privacy_policies');
        }
        if($request->saveType=='data_providers'){
            $input = $request->only('data_providers');
        }
        if($request->saveType=='software_licences'){
            $input = $request->only('software_licences');
        }
        if($request->saveType=='local_informations'){
            $input = $request->only('local_informations');
        }
        if($request->saveType=='OTD'){
            $validatedData = $request->validate([
                'OTD'   => 'required|array',
                'OTD.*' => 'required|string',
            ]);
            $input = $request->only('OTD');
        }
        
        $update = AppDefault::firstOrFail()->update($input);

        return response()->json('Successfully Updated');
    }

    public function offers()
    {
        $offers = Offer::orderBy('id','DESC')->get();
        return OfferResource::collection($offers);
    }

    public function addOffer(Request $request)
    {
        $validatedData = $request->validate([
            'offer_name'         => 'required|string',
            'offer_description'  => 'required|string',
            'offer_display_type' => 'required|numeric',
            'offer_image'        => 'required|mimes:jpeg,png|max:3072',
        ]);

        $offer = new Offer();
        $offer->name = $request['offer_name'];
        $offer->description = $request['offer_description'];
        $offer->display_type = $request['offer_display_type'];
        $offer->status = $request['status'];

        $offer->save();
            
        $image = $request->file('offer_image');
        $fileName = $offer->id.'_'.Str::random(15).'.'.$image->getClientOriginalExtension();
        $uploadDirectory = public_path('files'.DS.'offer_banners');
        $image->move($uploadDirectory, $fileName);

        Offer::where('id',$offer->id)->update(['image' => $fileName]);
        
        return response()->json('Successfully Added');
    }

    public function editOffer(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'display_type' => 'required|numeric',
        ]);

        $offer = Offer::findOrFail($id);
        $offer->update([
            'name'         => $request['name'],
            'description'  => $request['description'],
            'display_type' => $request['display_type'],
            'status'       => $request['status']
        ]);

        if ($request->hasFile('offer_image')) {
            $validator = Validator::make($request->all(), [
                'offer_image' => 'required|mimes:jpeg,png|max:3072',
            ]);
            $image = $request->file('offer_image');
            $fileName = $offer->id.'_'.Str::random(15).'.'.$image->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'offer_banners');
            $image->move($uploadDirectory, $fileName);

            $offer->update(['image' => $fileName]);
        }
        
        return response()->json('Successfully Updated');
    }

    public function changeOfferStatus(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required|digits:1',
            'id' => 'required|numeric',
        ]);
        $input = $request->only('status', 'id');

        $update = Offer::where('id',$input['id'])->update(['status' => $input['status']]);
        
        return response()->json('Status Changed');
    }
    
    public function deleteOffer($id)
    {
        $offer = Offer::findOrFail($id);

        $offer->delete();
        return response()->json('Offer Deleted');
    }
























    public function orderTime()
    {
        $appDefaults = AppDefault::first();
        $orderTime = $appDefaults->order_time;

        return response()->json($orderTime);
    }

    public function authUser()
    {
        $details = Auth::user();

        return response()->json($details);
    }

    public function sendPushNotification(Request $request)
    {
        $validatedData = $request->validate([
            'type'    => 'required|string',
            'subject' => 'required|max:30',
            'message' => 'required|max:300'
        ]);

        $subject = str_replace(' ', '_', $request->subject);

        $pushNotification = PushNotification::create([
            'type'      =>  $request->type,
            'subject'   =>  $request->subject,
            'message'   =>  $request->message
        ]);

        $notification = [
            'notifyType' => $subject,
            'message'    => $request->message,
            'model'      => 'PushNotification',
            'url'        => $pushNotification->id
        ]; 

        if($request->type==1){
            $ids = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'customer');
                          })
                         ->whereNotNull('fname')
                         ->whereNotNull('lname')
                         ->pluck('id');
        }
        elseif($request->type==2){
            $ids = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'driver');
                        })
                        ->pluck('id');
        }
        elseif($request->type==3){
            $ids = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'customer')
                                  ->orWhere('name', '=', 'driver');
                        })
                        ->whereNotNull('fname')
                        ->whereNotNull('lname')
                        ->pluck('id');
        }
        
        foreach($ids as $id){
            User::find($id)->AppNotification($notification);
        }

        $pushNotification->update([
            'status'    => 1,
            'sent_to'   =>  $ids
        ]);

        return response()->json([
            'message'   =>  'Push Notification Has been sent'
        ]);
    }

    public function sentPushNotification(Request $request)
    {
        $pushNotifications = PushNotification::orderBy('created_at','DESC')->paginate(config('settings.rows'));

        return response()->json($pushNotifications);
    }
}
