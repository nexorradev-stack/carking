<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Counter;
use Illuminate\Support\Facades\Log;
use App\Models\Car;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function sendInquiry(Request $request)
    {
       
        try {
            $validated = $request->validate([
                'advert_id' => 'required|integer',
                'advert_name' => 'required|string|max:255',
                'seller_email' => 'required|email',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone_number' => 'required|string',
                'message' => 'required|string',
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        }

        try {
           
            $car = Car::where('advert_id', $request->advert_id)->firstOrFail();
          
         
            $inquiry = Inquiry::create($validated);

            $data = $request->all();
            $data['car_slug'] = $car->slug;

           
            Mail::to($request->seller_email)->send(new \App\Mail\InquiryMail($data));
       
        } catch (\Exception $e) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }

        try {
          
            
            Counter::create([
                'advert_id' => $request->advert_id,
                'counter_type' => 'emailsu'
            ]);
           
        } catch (\Exception $e) {
           
        }

       
        return response()->json([
            'status' => 'success',
            'message' => 'Your details have been sent to the seller'
        ]);
    }
   public function sendInquiryDealer(Request $request)
{
   
   
 
    try {
        $validated = $request->validate([
            'dealer_email' => 'required|email',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);
   
    } catch (\Illuminate\Validation\ValidationException $e) {
        
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed: ' . implode(', ', array_merge(...array_values($e->errors())))
        ], 422);
    }

    try {
        
        $inquiryData = collect($validated)->except('dealer_email')->toArray();
        
       
        $inquiryData['advert_id'] = 0; 
        $inquiryData['advert_name'] = 'General Inquiry';
        $inquiryData['seller_email'] = $validated['dealer_email'];
        
        $data = $inquiryData;
        
      

        $inquiry = Inquiry::create($inquiryData);      
        $dealerEmail = $validated['dealer_email'];
        
     
        Mail::to($dealerEmail)->send(new \App\Mail\DealerInquiryMail($data));
   
        
    } catch (\Exception $e) {
       
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send email: ' . $e->getMessage()
        ], 500);
    }

   
    try {
        Counter::create([
            'counter_type' => 'general_inquiry',
            'created_at' => now()
        ]);
    } catch (\Exception $e) {
      
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Your inquiry has been sent successfully'
    ]);
}
}