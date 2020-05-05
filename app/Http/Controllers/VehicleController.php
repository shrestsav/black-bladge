<?php

namespace App\Http\Controllers;

use Validator;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VehicleController extends Controller
{
    public function index()
    {
        return 'test';
        $vehicles = Vehicle::paginate(20);

        return $vehicles;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_number' => 'required|unique:vehicles',
            'brand'          => 'required|string',
            'photo_file'     => 'required|file',
            'description'    => 'required|string',
        ]);

        //Save User Photo 
        if ($request->hasFile('photo_file')) {
            $image = Image::make($request->file('photo_file'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $fileName = Str::random(10).'.jpg';
            $uploadDirectory = public_path('files'.DS.'vehicles');
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);
        } 

        $vehicle = Vehicle::create([
            'model'             =>  1,
            'vehicle_number'    =>  $request->vehicle_number,
            'brand'             =>  $request->brand,
            'photo'             =>  $fileName,
            'description'       =>  $request->description,

        ]);

        return response()->json([
            'message' => 'Vehicle Added',
            'vehicle' =>  $vehicle
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validatedData = $request->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number,'.$id,
            'brand'          => 'required|string',
            'photo_file'     => 'nullable|file',
            'description'    => 'required|string',
        ]);
        
        $fileName = $vehicle->photo;
        //Save User Photo 
        if ($request->hasFile('photo_file')) {
            $image = Image::make($request->file('photo_file'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $fileName = Str::random(10).'.jpg';
            $uploadDirectory = public_path('files'.DS.'vehicles');
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);

            //Delete Old File
            $oldFile = public_path('files'.DS.'vehicles'.DS.$vehicle->photo);
            
            if(file_exists($oldFile)){
                \File::delete($oldFile);
            }
        } 

        $vehicle = $vehicle->update([
            'model'             =>  1,
            'vehicle_number'    =>  $request->vehicle_number,
            'brand'             =>  $request->brand,
            'photo'             =>  $fileName,
            'description'       =>  $request->description,

        ]);

        return response()->json([
            'message' => 'Vehicle Updated'
        ]);
    }
    
}
