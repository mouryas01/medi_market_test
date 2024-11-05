<?php

namespace App\Http\Controllers;

use App\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File; 
use Session;

class PromotionsController extends Controller
{
    public static $reference = "promotions";
    private $viewPath = "promotion.";

     /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
    }


    /**
     * Display a listing of Stores.    
     */
    public function index()
    {             
        $getPromotions = self::getPromotions();
        $data = [
            'title' => "Promotions and eCoupan",
            'promotions' => $getPromotions,
        ];        
        return view($this->viewPath."index")->with($data);
    }    
   

    /**
     * Add new store.          
     */
    public function create()
    {                   
        $users = self::$database->getReference('users')->getValue();  
        $data = [
            'title' => "Events",
            'users' => $users,
        ];
        
        return view($this->viewPath."create")->with($data);
    }


    /**
     * Store a newly created resource in storage.    
     */
    public function store(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'title' => "required",            
            'description' => "required",                  
            'picture' => "required|image"  
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {            
            $title = $request->input("title");          
            $description = $request->input("description");            
            
            $fileNameToStore = "";
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                $eventPicName = trim(str_replace(' ', '', $title));
                $fileNameToStore = $eventPicName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/promotions'), $fileNameToStore); 
                $image = 'uploads/promotions/'.$fileNameToStore;
            }
                   
            $newPost = self::$database
                    ->getReference(self::$reference)
                    ->push([                        
                        'title' => $title,                                       
                        'image' => $image,                    
                        'description' => $description,                       
                        'status' => 1,   //Active                      
                        'created_at' => time()   
                    ]);               
            
            return response()->json([
                    'success'=> "Promotion created successfully",
                    'uploaded_image' => '<img src="'.url($image).'" width="50px" height="50px"/>'
                ]);
        }
    }


    /**
     * Display the specified resource.     
     */
    public function show($id)
    {        
        $promotionData = self::$database->getReference(self::$reference."/".$id)->getValue(); 
        $promotion = [];
        if(!empty($promotionData)){   

            //Promotion info
            $promotion['title'] = $promotionData['title']; 
            $promotion['description'] = $promotionData['description'];        
            $promotion['image'] = $promotionData['image'];                                      
            $promotion['created_at'] = Carbon::createFromTimestamp($promotionData['created_at']);             
        }          

        $promotioninfo = json_decode(json_encode($promotion), FALSE);                    
        $data = [
            'title' => "Store",
            'promotion' =>  $promotioninfo,           
            'id' => $id
        ];   

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit promotion info.     
     */
    public function edit($id)
    {                                     
        $promotion = self::$database->getReference(self::$reference."/".$id)->getValue();                                
        $data = [
            'title' => "Promotion",
            'promotion' => $promotion,          
            'id' => $id
        ];   

        return view($this->viewPath."edit")->with($data);
    }


    /**
     * Update the specified resource in storage.   
     */
    public function update(Request $request, $id)
    {           
        $validator = Validator::make($request->all(), [            
            'title' => "required",           
            'description' => "required",                     
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $title = $request->input("title");          
            $description = $request->input("description");                         
            $image = $request->input("old_picture"); 

            $fileNameToStore = "";    
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {

                //Remove previous pic
                if(file_exists($image)) 
                {
                    unlink($image);
                    //echo "File Successfully Delete."; 
                }

                //Upload new pic
                $eventPicName = trim(str_replace(' ', '', $title));
                $fileNameToStore = $eventPicName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/promotions'), $fileNameToStore); 
                $image = 'uploads/promotions/'.$fileNameToStore;               
            }

            $updateProduct = self::$database
                ->getReference(self::$reference."/".$id)
                ->update([                  
                    'title' => $title,                                 
                    'image' => $image,                    
                    'description' => $description,                   
                    'status' => 1,   //Active                                
                    'created_at' => time()   
                ]);
            
            return response()->json(['success'=> "Promotion Details updated successfully"]);
        }
    }


    /**
     * Remove the specified resource from storage.   
     */
    public function destroy($id)
    {
        $data = self::$database->getReference(self::$reference."/".$id)->getValue();
        $delete = file_exists(public_path($data['image'])) ? unlink(public_path($data['image'])):'';
        self::$database->getReference(self::$reference."/".$id)->remove();       
        return back();
    }


     /**
     * Fetch all categories llst
     */
    public function getPromotions() : array {
     
        $fetchPromotions = self::$database->getReference(self::$reference)->getValue();

        $promotions = [];
        if(!empty($fetchPromotions)){

            //Set as descending order 
            $fetchPromotions = array_reverse($fetchPromotions, true);  

            foreach ($fetchPromotions as $key => $value){                
                if(empty($value)) {
                    continue;
                }                   
                $promotion_key = trim(trim('"'.$key.'"','"'));   
                $promotion = new Promotion();
                $promotion->promotion_key = $promotion_key;
                $promotion->title = $value['title'];                   
                $promotion->description = $value['description'];                 
                $promotion->image = $value['image'];    
                $promotion->status = $value['status'];                                                                                                 
                $promotion->created_at = Carbon::createFromTimestamp($value['created_at']);                 
                array_push($promotions, $promotion);
            }                   
        }        
        return $promotions;
    }
    
    
    /**
     * Fetch all Events, filter by date
     */
    public function eventFilterByDate(request $request)
    {      
        $uid = Session::get('uid');             
        $validator = Validator::make($request->all(), [
            'startdate' => "required",
            'enddate' => "required"  
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors()->all());       
        }
        else
        {                        
            $startdate = $request->input("startdate");
            $enddate = $request->input("enddate"); 
            
            $fetchEvents = self::$database->getReference(self::$reference)->getValue();
            $events = [];
            if(!empty($fetchEvents)){
    
                //Set as descending order 
                $fetchEvents = array_reverse($fetchEvents, true);  
    
                foreach ($fetchEvents as $key => $value){                
                    if(empty($value)) {
                        continue;
                    }              
                    $dstartdate = !empty($value['start_date']) ? date('m/d/Y', strtotime($value['start_date'])):'';
                    $denddate = !empty($value['end_date']) ? date('m/d/Y', strtotime($value['end_date'])):'';
                    $event_key = trim(trim('"'.$key.'"','"'));

                    //filter by order date
                    if(strtotime($startdate) <= strtotime($dstartdate) && strtotime($enddate) >= strtotime($denddate))	                           
                    {
                        $event = new Event();
                        $event->event_key = $event_key;
                        $event->description = $value['description']; 
                        $event->address = $value['address']; 
                        $event->title = $value['title'];                
                        $event->event_id = $value['event_id'];                                     
                        $event->image = $value['image'];     
                        $event->start_date = date('d-m-Y', strtotime($value['start_date']));
                        $event->start_time = $value['start_time'];
                        $event->end_date = date('d-m-Y', strtotime($value['end_date']));
                        $event->end_time = $value['end_time'];
                        $event->created_at = Carbon::createFromTimestamp($value['created_at']);                 
                        array_push($events, $event);
                    }
                }                   
            }  
            //dd($events);
        }        
        
        $data = [
            'title' => "Events",
            'events' => $events,
        ];      
        return view($this->viewPath."index")->with($data);
    }
    
    
}
