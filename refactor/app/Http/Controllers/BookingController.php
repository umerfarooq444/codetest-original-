<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
* Class BookingController
* @package DTApi\Http\Controllers
*/
class BookingController extends Controller
{
    //remove repetition of the code
    public $status;
    public $message;
    public $data;
    /**
    * @var BookingRepository
    */
    protected $repository;
    
    /**
    * BookingController constructor.
    * @param BookingRepository $bookingRepository
    */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
        //remove repetition of the code
        $this->data=[];
        $this->status=true;
        $this->message='Something is going wrong please try again!';
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function index(Request $request)
    {
            //Single responsibility principle
            //A class and a method should have only one responsibility.

        try{
            //if user id exist then get single user job
            //if user with adminstrator role want single record he can get it
            //if user id does not set and user with adminstrator role he can fetch all users

            $this->data['response']=($this->isAdministrtorRole($request) &&  !$request->has('user_id')) ?  $this->getAllJob($request) : $this->getJob($request->user_id);
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());

        }catch(\Exception $e){
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }

        
    }
    public function standerResponse(){
      return  ['Status'=>$this->status,'Message'=>$this->message,'Data'=>$this->data];
    }
    public function isAdministrtorRole(){
      return  $this->isAdmin() || $this->isSuperAdmin();
    }
    public function getJob($user_id){
       return  $this->repository->getUsersJobs($user_id);
    }
   public function getAllJob($request){
       return  $this->repository->getAll($request);
    }

    /**
    * @param $id
    * @return mixed
    */
    public function show($id)
    {  
        // use findOrFail instead of find function
        try
        {  
            $this->data['response']=$this->repository->with('translatorJobRel.user')->findOrFail($id);
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function store(StoreBookingRequest  $request)
    {
        //make a seperate validationRequest for booking and validate
        // correct syntax to fetch authenticated user

        $validated = $request->validated();
        
        try {
            $this->data['response'] = $this->repository->store(Auth::user(), $request->validated());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());  
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    /**
    * @param $id
    * @param Request $request
    * @return mixed
    */
    public function update($id, StoreBookingRequest $request)
    {
        // validatie form with request class

        $validated = $request->validated();
        
        try {
            $this->data['response'] = $this->repository->updateJob($id, array_except($request->validated(), ['_token', 'submit']), Auth::user());  
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse()); 
        } 
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
    
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function immediateJobEmail(Request $request)
    {
             
        try
        {
            $adminSenderEmail = config('app.adminemail');
            $data = $request->all();
            $this->data['response'] = $this->repository->storeJobEmail($data);
            
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse()); 
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
        
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function getHistory(Request $request)
    {
        // use shorthand notation to get request data
        // if authenticated user id is equal to user id in request then call jobHistory method

        try{
            $this->data['response'] = Auth::user()->id == $request->user_id ? $this->jobsHistory($request) : null; 
            return response()->json($this->standerResponse()); 
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
    }

    public function jobsHistory($request){
        $this->message='Response fetched Successfully!';
        return $this->repository->getUsersJobsHistory(Auth::user()->id, $request);
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function acceptJob(Request $request)
    {
        // pass data directly in arguments. no need to create variables.

        try{
            $this->data['response'] = $this->repository->acceptJob($request->all(), Auth::user());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse()); 
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    public function acceptJobWithId(Request $request)
    {
        // pass data directly in arguments. no need to create variables.

        try{
            $this->data['response'] = $this->repository->acceptJobWithId($request->job_id, Auth::user());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function cancelJob(Request $request)
    {
        // pass data directly in arguments. no need to create variables.

        try{
            $this->data['response'] = $this->repository->cancelJobAjax($request->all(), Auth::user());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function endJob(Request $request)
    {
        // pass data directly in arguments. no need to create variables.

        try{
            $this->data['response'] = $this->repository->endJob($request->all());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    public function customerNotCall(Request $request)
    {
        // pass data directly in arguments. no need to create variables.

        try{
            $this->data['response'] = $this->repository->customerNotCall($request->all());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());

        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
        
    }
    
    /**
    * @param Request $request
    * @return mixed
    */
    public function getPotentialJobs(Request $request)
    {
        $data = $request->all();
        try{
            $this->data['response'] = $this->repository->getPotentialJobs(Auth::user());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
    }
    
    public function distanceFeed(StoreDistancefeedRequest $request)
    {
        $validated = $request->validated();

        $gtDistance = false;
        $getJob = false;


        $data = $request->all();
        
        if ($request->has('distance') && notEmpty($data['distance'])) {
            $gtDistance = true;
        }
        else if ($request->has('time') && notEmpty($data['time'])) {
            $gtDistance = true;
        }
        else if ($request->has('jobid') && notEmpty($data['jobid'])) {
            $jobid = $data['jobid'];
        }
        
        else if ($request->has('session_time') && notEmpty($data['session_time'])) {
            $getJob = true;
        }
        
        else if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $getJob = true;
        }
        
        else if ($data['manually_handled'] == 'true') {
            $getJob = true;
        }
        
        else if ($data['by_admin'] == 'true') {
            $getJob = true;
        }
        
        else if ($request->has('admincomment') && notEmpty($data['admincomment'])) {
            $getJob = true;
        }


        if ($gtDistance) {
            
            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $request->distance, 'time' => $request->time));
        }
        
        if ($getJob) {
            
            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $request->admincomment, 'flagged' => $request->flagged, 'session_time' => $request->session, 'manually_handled' => $request->manually_handled, 'by_admin' => $request->by_admin));
            
        }
        
        $this->message='Response fetched Successfully!';
        return response()->json($this->standerResponse());
    }
    
    public function reopen(Request $request)
    {
        
        try{
            $this->data['response'] = $this->repository->reopen($request->all());
            $this->message='Response fetched Successfully!';
            return response()->json($this->standerResponse());
        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
        
       
    }
    
    public function resendNotifications(Request $request)
    {
        //pass jobid directly from request method

        $job = $this->repository->find($request->jobid);
        try{
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            $this->message='Push sent';
            return response()->json($this->standerResponse());

        }
        catch(\Exception $e)
        {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }

        
    }
    
    /**
    * Sends SMS to Translator
    * @param Request $request
    * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
    */
    public function resendSMSNotifications(Request $request)
    {
    
        $job = $this->repository->find($request->jobid);
        $job_data = $this->repository->jobToData($job);
        
        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            $this->message='SMS sent';
            return response()->json($this->standerResponse());
        } catch (\\Exception $e) {
            $this->message=$e->getMessage();
            return response()->json($this->standerResponse());
        }
    }
    

    public function isAdmin(){
        
        return Auth::user()->user_type == env('ADMIN_ROLE_ID') ? true : false;

    }

    public function isSuperAdmin(){

        return Auth::user()->user_type == env('SUPERADMIN_ROLE_ID') ? true : false;

    }
    
    
}
