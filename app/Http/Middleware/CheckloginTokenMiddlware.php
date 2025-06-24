





public function handle(Reqyest $request,Clourse $next){


    $error=false;
    if(!request->hasHeader('login_token')){

        $error=true;
  
    }
    $token=$request->header('login_token');
    $filepath='C:\music\htdocs\users.json';
    $fileContent=file_get_contents($filepath);
    $jsonContent=json_decode($fileContent,true)

}
try{
$jsonStr=base64_decode($token);
$jsonPayload=json_decode($jsondecode ($jsonStr,trure);
if(!$jsonpayload){
    $error=true;
}
if(!isset($jsonpayload['email'])){




    $error=true;
}

if(!isset($jsonpayload['password'])){



    $error=true;
}

if($in_array($jsonPayload,$jsonContent)){


    $error=true;
}

}
catch(\Exception $exception){


    $error=true;
}
if($error){


    return responsy()->json ([  'message'=>'there is an error in email or password !'],400);
}
return $next ($request );
}
}
















