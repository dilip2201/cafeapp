<?php

namespace App\Http\Controllers;

use App\LeaveType;
use App\User;
use Illuminate\Http\Request;
use App\Holiday;
use App\LeaveApply;
use App\SalaryType;
use Auth;
use App\Client;
use App\Group;
use App\School;
use App\Item;
use App\Company;
class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {      
        $company = Company::first();
        return view('admin.dashboard.index',compact('company'));
    }

    
    public function loadimages(Request $request)
    { 
        $school = $request->school;
        $gender = $request->gender;
        $season = $request->season;
        $standard = $request->standard;
        $item = $request->item;
        $images = getimagesof($item,$school,$gender,$season,$standard);

        $return = '<div class="slickslide" style="text-align:center;">';
        if(!empty($images)){
            foreach($images as $image){
                $singleabel = '';
                if(!empty($image->itemname->name)){
                  $singleabel = $image->itemname->name.'('.$image->itemname->ract_number.')';
                }

                if(!empty($image->file) && file_exists(public_path().'/uniforms/'.$image->file)){
                    $imagefile = url('public/uniforms/'.$image->file);
                } else{
                    $imagefile = url('public/uniforms/default.png');
                }

                $return .= '<div>
                            <img style="height:200px; max-width:300px; width: auto;" src="'.$imagefile.'"  />
                            <label style="margin-bottom: 0px; width: 100%;">'.$singleabel.'</label>
                            <label style="font-weight: 100; margin-bottom: 0px; width: 100%;" >'.$image->remarks.'</label>
                           </div>';
            }
        }
                
        $return .= '</div>';
        return $return;
    }
    public function filterdata(Request $request)
    {     
        $school = $request->school;
        $gender = $request->gender;
        $season = $request->season;
        $standard = $request->standard;
        if(!empty($request->items)){
            $items = Item::whereHas('uniforms', function($q) use($school,$gender,$season,$standard){
                        $q->where('school_id', $school);
                        $q->where('gender', $gender);
                        $q->where('season', $season);
                        $q->where('standard', $standard);
                    })->whereIn('id',$request->items)->get();
        }else{
            $items = Item::whereHas('uniforms', function($q) use($school,$gender,$season,$standard){
                        $q->where('school_id', $school);
                        $q->where('gender', $gender);
                        $q->where('season', $season);
                        $q->where('standard', $standard);
                    })->get();
        }

       
        return view('admin.dashboard.loaddata',compact('items','school','gender','season','standard'));
    }

    public function company(Request $request)
    {     
        $company = Company::first();
        return view('admin.dashboard.company',compact('company'));
    }

    
}
