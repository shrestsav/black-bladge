<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Order;
use App\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\Driver\Driver as DriverResource;

class DriverController extends Controller
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'driver');
                        })
                        ->with('vehicle')
                        ->paginate(Session::get('rows'));

        return DriverResource::collection($drivers);
    }

    public function allDrivers()
    {
        $drivers = User::select('id', 'fname', 'lname')
                        ->whereHas('roles', function ($query) {
                            $query->where('name', '=', 'driver');
                        })
                        ->get();

        return response()->json($drivers);
    }

    public function driverOrders(Request $request, $driver_id)
    {
        $this->validate($request, [
            'type'     => 'required|string',
            'job_type' => 'required|string'
        ]);

        $orders = Order::with('details','customer:id,fname,lname','pick_location_details.mainArea');

        if($request->job_type=='pick'){
            $orders = $orders->where('driver_id',$driver_id)
                             ->where(function ($query) use ($driver_id){
                                $query->where('drop_driver_id','!=',$driver_id)
                                      ->orWhereNull('drop_driver_id');
                             });
        }
        elseif($request->job_type=='drop'){
            $orders = $orders->where('drop_driver_id',$driver_id)
                             ->where(function ($query) use ($driver_id){
                                $query->where('driver_id','!=',$driver_id)
                                      ->orWhereNull('driver_id');
                             });
        }
        elseif($request->job_type=='pick_drop'){
            $orders = $orders->where('driver_id',$driver_id)
                             ->where('drop_driver_id',$driver_id);
        }
        elseif($request->job_type=='any'){
            $orders = $orders->where(function ($query) use ($driver_id){
                                $query->where('driver_id',$driver_id)
                                      ->orWhere('drop_driver_id',$driver_id);
                            });
        }
        
        if($request->type=='monthly'){
            $this->validate($request, [
                'year_month' => 'required|string'
            ]);
            $year_month = explode('-',$request->year_month);
            $orders = $orders->whereYear('created_at', '=', $year_month[0])
                             ->whereMonth('created_at','=', $year_month[1]);
        }
        elseif($request->type=='yearly'){
          $this->validate($request, [
              'year' => 'required|string',
          ]);
          $orders = $orders->whereYear('created_at', '=', $request->year);
        }

        $orders = $orders->paginate(Session::get('rows'));
                        
        $orders->setCollection( $orders->getCollection()->makeVisible('total_amount'));

        $driverDetails = User::find($driver_id);

        return response()->json([
            'driver'      =>  $driverDetails,
            'orders'      =>  $orders,
            'orderStatus' => config('settings.orderStatuses')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'fname'      => 'required|max:191',
            'email'      => 'nullable|email|max:191|unique:users',
            'phone'      => 'nullable|unique:users',
            'username'   => 'required|string|max:191',
            'license_no' => 'required|string|max:191',
            'vehicle_id' => 'required|numeric|exists:vehicles,id|unique:users,vehicle_id',
            'photo_file' => 'nullable|mimes:jpeg,jpg,bmp,png|max:15072',
            'password'   => 'required|string|min:4|max:4',
        ]);
        
        $driver = User::create([
            'username'    =>  $request->username,
            'license_no'  =>  $request->license_no,
            'vehicle_id'  =>  $request->vehicle_id,
            'gender'      =>  $request->gender, 
            'fname'       =>  $request->fname,
            'lname'       =>  $request->lname,
            'phone'       =>  $request->phone,
            'email'       =>  $request->email,
            'dob'         =>  $request->dob,
            'country'     =>  $request->country,
            'password'    =>  Hash::make($request->password),
        ]); 

        $role_id = Role::where('name','driver')->first()->id;

        // Assign as Driver
        $driver->attachRole($role_id);
        
        //Save User Photo 
        if ($request->hasFile('photo_file')) {
            $image = Image::make($request->file('photo_file'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $fileName = 'dp_user_'.$driver->id.'.jpg';
            $uploadDirectory = public_path('files'.DS.'users'.DS.$driver->id);
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);

            $driver->update([
                'photo' => $fileName
            ]);
        } 


        return response()->json(['message'=>'Successfully Added']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id'         => 'required',
            'fname'      => 'required|max:191',
            'lname'      => 'nullable|max:191',
            'username'   => 'required|string|max:191',
            'license_no' => 'required|string|max:191',
            'vehicle_id' => 'required|numeric|exists:vehicles,id|unique:users,vehicle_id',
            'email'      => 'nullable|email|max:191|unique:users,email,'.$id,
            'phone'      => 'nullable|unique:users,phone,'.$id,
            'photo_file' => 'nullable|mimes:jpeg,jpg,bmp,png|max:15072',
            'password'   => 'nullable|string|min:4|max:4',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            return response()->json([
                'status'  => '422',
                'message' => 'Validation Failed',
                'errors'  => $error,
            ], 422);
        }

        $driver = User::findOrFail($request->id);
        $password = $driver->password;
        $fileName = $driver->photo;

        //Save User Photo 
        if ($request->hasFile('photo_file')) {
            $image = Image::make($request->file('photo_file'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $fileName = 'dp_user_'.$id.'.jpg';
            $uploadDirectory = public_path('files'.DS.'users'.DS.$id);
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);
        } 

        $driver = $driver->update([
            'username'    =>  $request->username,
            'license_no'  =>  $request->license_no,
            'vehicle_id'  =>  $request->vehicle_id,
            'gender'      =>  $request->gender, 
            'fname'       =>  $request->fname,
            'lname'       =>  $request->lname,
            'phone'       =>  $request->phone,
            'email'       =>  $request->email,
            'dob'         =>  $request->dob,
            'country'     =>  $request->country,
            'photo'       =>  $fileName,
            'password'    =>  $request->password ? Hash::make($request->password) : $password,
        ]);
        
        return response()->json([
            'status' => '200',
            'message' => 'Updated Successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
