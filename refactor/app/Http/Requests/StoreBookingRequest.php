class StoreBookingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|unique:bookings|max:255',
            'body' => 'required',
        ];
    }
}