<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
     // Start a subscription payment
     public function startSubscription(Request $request)
    {
        try{

            $user = Auth::user();  // Assuming the user is authenticated

            // Validate incoming data
            $validated = $request->validate([
                'subscription_type' => 'required|in:monthly,3_months,6_months,1_year',
            ]);
    
            // Determine the amount based on subscription type
            $amount = 0;
            $nextPaymentDate = Carbon::now();
            
            switch ($validated['subscription_type']) {
                case 'monthly':
                    $amount = 5;
                    $nextPaymentDate = $nextPaymentDate->addMonth();
                    break;
                case '3_months':
                    $amount = 1500;
                    $nextPaymentDate = $nextPaymentDate->addMonths(3);
                    break;
                case '6_months':
                    $amount = 2800;  // Discounted rate
                    $nextPaymentDate = $nextPaymentDate->addMonths(6);
                    break;
                case '1_year':
                    $amount = 5500;  // Discounted rate
                    $nextPaymentDate = $nextPaymentDate->addYear();
                    break;
            }
    
            // Call SasaPay API to create a payment request
            $response = $this->requestPayment($user, $amount);
    
            // Check if the payment request is successful
            if ($response['status'] === true) {
                // Store the payment record with payment_request_id
                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'status' => 'pending',
                    'payment_date' => Carbon::now(),
                    'next_payment_date' => $nextPaymentDate,
                    'MerchantRequestID' => $response['MerchantRequestID'], // Save the PaymentRequestID here
                ]);
                return response()->json(['message' => 'Subscription started, please complete the payment']);
            }

            return response()->json([
                'error' => 'Failed to initiate payment',
                'details' => $response // This will include the actual response details from the SasaPay API
            ], 400);

        }catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
       
    }

    // Check if the user has an active subscription
    public function hasActiveSubscription()
    {
        $user = Auth::user();  // Get authenticated user
        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($payment && $payment->next_payment_date > Carbon::now()) {
            return response()->json(['message' => 'Active subscription found']);
        }

        return response()->json(['message' => 'No active subscription'], 400);
    }
 
     // Request payment from SasaPay API
     private function requestPayment($user, $amount)
     {
         $url = 'https://sandbox.sasapay.app/api/v1/payments/request-payment/';
         $headers = [
             'Authorization' => 'Bearer ' . $this->getAccessToken(),
         ];
 
         $data = [
             'MerchantCode' => '600980', // Your Merchant Code
             'NetworkCode' => '63902', // Use appropriate code (M-Pesa, Airtel, etc.)
            //  'PhoneNumber' => $user->phoneNumber, // User's phone number
             'PhoneNumber' => '0729736134', // User's phone number
             'TransactionDesc' => 'Subscription payment',
             'AccountReference' => $user->id . '-' . time(),
             'Currency' => 'KES', // Currency
             'Amount' => $amount,
             'CallBackURL' => url('/payment/callback'), // URL to handle the callback after payment
         ];
 
         // Send request to SasaPay API
         $response = Http::withHeaders($headers)->post($url, $data);
         
         // Return the API response
         return $response->json();
     }
 
     // Callback to handle payment confirmation
    // Callback to handle payment confirmation
    public function paymentCallback(Request $request)
    {
        // Log the entire incoming request to inspect the callback response
        Log::info('Payment Callback Response:', $request->all());

        $paymentRequestID = $request->input('MerchantRequestID');
        $resultCode = $request->input('ResultCode');
        $transactionAmount = $request->input('TransAmount');

        // Find the payment record by PaymentRequestID
        $payment = Payment::where('MerchantRequestID', $paymentRequestID)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        // Update the payment status based on the result code
        if ($resultCode == 0) {  // Successful payment
            $payment->status = 'active';
            $payment->save();

            Log::info('Payment successful:', [
                'paymentRequestID' => $paymentRequestID,
                'amount' => $transactionAmount,
                'status' => 'active',
            ]);

            return response()->json(['message' => 'Payment successful, subscription active']);
        } else {
            $payment->status = 'failed';
            $payment->save();

            Log::info('Payment failed:', [
                'paymentRequestID' => $paymentRequestID,
                'resultCode' => $resultCode,
                'status' => 'failed',
            ]);

            return response()->json(['error' => 'Payment failed, please try again'], 400);
        }
    }
 
     // Get access token to make requests to SasaPay API
     private function getAccessToken()
     {
         $url = 'https://sandbox.sasapay.app/api/v1/auth/token/?grant_type=client_credentials';
        
         $clientId = env('SASAPAY_CLIENT_ID');  // Retrieve client ID from .env file
         $clientSecret = env('SASAPAY_CLIENT_SECRET'); 
 
         $headers = [
             'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
         ];
 
         $response = Http::withHeaders($headers)->get($url);
 
         $data = $response->json();
         
         return $data['access_token'];
     }
}
