<?php
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
 
class MailController extends Controller
{
    public function send(Request $req)
    {
        $json = $req->input('json', null);
  
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true);//array
        
        Mail::send('emails.mail', $params_array, function ($message) {
            $message->from('prensa@clinicaltopicsinmedicine.com');
            $message->to('prensa@clinicaltopicsinmedicine.com');
        });

        $data = array(
            'code' => 200,
            'status' => 'success',
            'message' => 'Mensaje enviado correctamente'
          );

        return response()->json($data, $data['code']);

        
    }
}
