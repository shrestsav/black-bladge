<?php

namespace App\Http\Controllers;

use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard');
    }

    public function content()
    {
        return view('content');
    }


    public function getChartData(Request $request){
        $date;
        switch($request->type){
            case 'PastWeek':
                $date = Carbon::now()->subWeek(1);
            break;
            case 'PastMonth':
                $date = Carbon::now()->subMonth(1);
            break;
            case 'PastYear':
                $date = Carbon::now()->subYear(1);
            break;
            default:
                $date = Carbon::now()->subWeek(1);
            break;
        }

        if($request->type == 'AllTime'){
            $orders = Order::select(DB::raw('status,count(*) as count'))->groupBY('status')->get();
            
        }else{
            $orders = Order::select(DB::raw('status,count(*) as count'))->where(function($query) use($date){
                $query->where('updated_at', '>' , $date)
                    ->orWhere('created_at', '>' , $date);
            })->groupBY('status')->get();
        }
        return response()->json($orders);
    }
}
