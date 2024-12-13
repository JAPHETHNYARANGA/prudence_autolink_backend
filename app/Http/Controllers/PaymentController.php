<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
     // Start a subscription payment
     public function startSubscription(Request $request)
     {
         $user = Auth::user();  // Assuming the user is authenticated
 
         // Validate incoming data
         $validated = $request->validate([
             'amount' => 'required|numeric|min:0',
         ]);
 
         $amount = $validated['amount'];
         
         // Call SasaPay API to create a payment request
         $response = $this->requestPayment($user, $amount);
 
         // Check if the payment request is successful
         if ($response['status'] === true) {
             // Store the payment record
             Payment::create([
                 'user_id' => $user->id,
                 'amount' => $amount,
                 'status' => 'pending',
                 'payment_date' => Carbon::now(),
                 'next_payment_date' => Carbon::now()->addMonth(),
             ]);
             return response()->json(['message' => 'Subscription started, please complete the payment']);
         }
 
         return response()->json(['error' => 'Failed to initiate payment'], 400);
     }
 
     // Request payment from SasaPay API
     private function requestPayment($user, $amount)
     {
         $url = 'https://sandbox.sasapay.app/api/v1/payments/request-payment/';
         $headers = [
             'Authorization' => 'Bearer ' . $this->getAccessToken(),
         ];
 
         $data = [
             'MerchantCode' => '60***0', // Your Merchant Code
             'NetworkCode' => '0', // Use appropriate code (M-Pesa, Airtel, etc.)
             'PhoneNumber' => $user->phone, // User's phone number
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
     public function paymentCallback(Request $request)
     {
         $paymentRequestID = $request->input('PaymentRequestID');
         $resultCode = $request->input('ResultCode');
         $transactionAmount = $request->input('TransAmount');
 
         // Update the payment status based on the result code
         $payment = Payment::where('payment_request_id', $paymentRequestID)->first();
 
         if ($resultCode == 0) {  // Successful payment
             $payment->status = 'active';
             $payment->next_payment_date = Carbon::now()->addMonth();
             $payment->save();
 
             return response()->json(['message' => 'Payment successful, subscription active']);
         } else {
             $payment->status = 'failed';
             $payment->save();
 
             return response()->json(['error' => 'Payment failed, please try again'], 400);
         }
     }
 
     // Get access token to make requests to SasaPay API
     private function getAccessToken()
     {
         $url = 'https://sandbox.sasapay.app/api/v1/auth/token/?grant_type=client_credentials';
         $clientId = 'CLIENT_ID'; // Replace with actual client ID
         $clientSecret = 'CLIENT_SECRET'; // Replace with actual client secret
 
         $headers = [
             'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
         ];
 
         $response = Http::withHeaders($headers)->get($url);
 
         $data = $response->json();
         
         return $data['access_token'];
     }
}
