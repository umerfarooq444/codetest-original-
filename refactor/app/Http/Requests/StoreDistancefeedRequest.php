class StoreDistancefeedRequest extends FormRequest
{
    public function rules()
    {
        return [
            'distance' => 'nullable',
            'time' => 'nullable',
            'jobid' => 'nullable',
            'session_time' => 'nullable',
            'flagged' => 'required|boolean',
            'admincomment' => 'nullable',
            'manually_handled' => 'required|boolean',
            'by_admin' => 'required|boolean',
            'admincomment' => 'nullable',
           
        ];
    }
}