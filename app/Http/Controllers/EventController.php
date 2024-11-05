<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File; 
use Session;

class EventController extends Controller
{
    public static $reference = "events";
    private $viewPath = "event.";

     /**
     * Categories Controller constructor.
     */
    public function __construct()
    {
        $this->middleware("firebase");
        self::$database = self::firebaseDatabaseInstance();
        
        //Auto Deactivate event 
        $getEvents = self::getEvents();
        $current_date = strtotime(date('d-m-Y'));        
        foreach($getEvents as $event){   
             $end_date = strtotime($event['end_date']);  
             $event_id = $event['event_key'];                    
             if($current_date > $end_date){                              
                 $eventData = self::$database->getReference(self::$reference."/".$event_id)->getValue();
                 if($eventData){
                     //Status 2 = Dactivate
                     self::$database->getReference(self::$reference."/".$event_id)->getChild('status')->set(2);   
                 }                
             }            
        }
        //////////////////////////
    }


    /**
     * Display a listing of Stores.    
     */
    public function index()
    {                     
        $getEvents = self::getEvents();

        $data = [
            'title' => "Events",
            'events' => $getEvents,
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
            'start_date' => "required|string",
            'start_time' => "required|string",
            'end_date' => "required|string",
            'end_time' => "required|string",
            'description' => "required",  
            'address' =>  "required",        
            'picture' => "required|image"  
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            //check last exist event id
            $events = self::$database->getReference(self::$reference)->orderByChild('event_id')->getValue();  
            $lastkey = !empty($events) ? end($events):'';
            $event_id = !empty($lastkey) ? $lastkey['event_id']+1 :'1';

            $title = $request->input("title");
            $start_date = $request->input("start_date");
            $start_time = $request->input("start_time");
            $end_date = $request->input("end_date");
            $end_time = $request->input("end_time");
            $description = $request->input("description");
            $address = $request->input("address");
            
            $fileNameToStore = "";
            if($request->hasFile("picture") && $request->file("picture")->isValid()) {
                $eventPicName = trim(str_replace(' ', '', $title));
                $fileNameToStore = $eventPicName.".".$request->file("picture")->getClientOriginalExtension();                
                $request->file("picture")->move(public_path('uploads/events'), $fileNameToStore); 
                $image = 'uploads/events/'.$fileNameToStore;
            }
                   
            $newPost = self::$database
                    ->getReference(self::$reference)
                    ->push([
                        'event_id' => $event_id,
                        'title' => $title,
                        'start_date' => $start_date,
                        'start_time' => $start_time,
                        'end_date' => $end_date,    
                        'end_time' => $end_time,                    
                        'image' => $image,                    
                        'description' => $description, 
                        'address' => $address, 
                        'status' => 1,   //Active                      
                        'created_at' => time()   
                    ]);

            if($newPost){
                 //Notification Sending         
                $users = self::$database->getReference('fd_users')->getValue(); 
                foreach($users as $user){
                    if(!empty($user['device_token'])){
                        $this->send_notification($user['device_token'], $title, $description); 
                    } 
                }       
            }        
            
            return response()->json([
                    'success'=> "Event created successfully",
                    'uploaded_image' => '<img src="'.url($image).'" width="50px" height="50px"/>'
                ]);
        }
    }


    /**
     * Display the specified resource.     
     */
    public function show($id)
    {        
        $eventData = self::$database->getReference(self::$reference."/".$id)->getValue(); 
        $event = [];
        if(!empty($eventData)){   

            //Store info
            $event['event_id'] = $eventData['event_id']; 
            $event['title'] = $eventData['title']; 
            $event['description'] = $eventData['description'];
            $event['address'] = $eventData['address'];
            $event['image'] = $eventData['image'];                   
            $event['start_date'] = date('d-m-Y', strtotime($eventData['start_date']));  
            $event['start_time'] = $eventData['start_time'];                               
            $event['end_date'] = date('d-m-Y', strtotime($eventData['end_date']));    
            $event['end_time'] = $eventData['end_time'];                
            $event['created_at'] = Carbon::createFromTimestamp($eventData['created_at']); 

            //Joiner Info
            // $store['vendor_id'] = $storeData['vendor_id'];
            // $vendorData = self::$database->getReference('join_events'."/".$storeData['vendor_id'])->getValue(); 
            // if(!empty($vendorData)){
            //     $store['vendor_email'] = $vendorData['email'];
            //     $store['vendor_name'] = $vendorData['name'];
            //     $store['vendor_pic'] = $vendorData['picture'];                
            // }
        }          
        $eventinfo = json_decode(json_encode($event), FALSE);                    
        $data = [
            'title' => "Store",
            'event' =>  $eventinfo,           
            'id' => $id
        ];   
        //dd($data); 

        return view($this->viewPath."show")->with($data);
    }


    /**
     * Edit Product info.     
     */
    public function edit($id)
    {                                     
        $event = self::$database->getReference(self::$reference."/".$id)->getValue();                                
        $data = [
            'title' => "Store",
            'events' => $event,          
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
            'start_date' => "required|string",
            'start_time' => "required|string",
            'end_date' => "required|string",
            'end_time' => "required|string",
            'description' => "required", 
            'address'  => "required",          
        ]);

        if ($validator->fails())
        {
            return response()->json(['error'=> $validator->errors()->all()]);
        }
        else
        {
            $title = $request->input("title");
            $start_date = $request->input("start_date");
            $start_time = $request->input("start_time");
            $end_date = $request->input("end_date");
            $end_time = $request->input("end_time");
            $description = $request->input("description");  
            $address = $request->input("address"); 

            $old_event_id = $request->input("old_event_id");    
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
                $request->file("picture")->move(public_path('uploads/events'), $fileNameToStore); 
                $image = 'uploads/events/'.$fileNameToStore;               
            }

            $updateProduct = self::$database
                ->getReference(self::$reference."/".$id)
                ->update([
                    'event_id' => $old_event_id,
                    'title' => $title,
                    'start_date' => $start_date,
                    'start_time' => $start_time,
                    'end_date' => $end_date, 
                    'end_time' => $end_time,                    
                    'image' => $image,                    
                    'description' => $description,
                    'address' => $address, 
                    'status' => 1,   //Active                                
                    'created_at' => time()   
                ]);
            
            return response()->json(['success'=> "Event updated successfully"]);
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
    public function getEvents() : array {
     
        $fetchEvents = self::$database->getReference(self::$reference)->getValue();

        $events = [];
        if(!empty($fetchEvents)){

            //Set as descending order 
            $fetchEvents = array_reverse($fetchEvents, true);  

            foreach ($fetchEvents as $key => $value){                
                if(empty($value)) {
                    continue;
                }                   
                $event_key = trim(trim('"'.$key.'"','"'));   
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
                $event->status = $value['status'];
                $event->created_at = Carbon::createFromTimestamp($value['created_at']);                 
                array_push($events, $event);
            }                   
        }        
        return $events;
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
