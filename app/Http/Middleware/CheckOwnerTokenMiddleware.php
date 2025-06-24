<?php

$error=false;
$productId=$request->route('productId');

$filepath='C:\music\htdocs\users.json';
$fileContent=file_get_contents($filepath);
$jsonContent=json_decode($fileContent,true)
try{
$jsonStr=base64_decode($token);
$jsonPayload=json_decode($jsondecode ($jsonStr,trure);


if(!$jsonPayload){



    $error=true;
}

if(!isset($jsonpayload['email'])){



    $error=true;
}

if($jsonPayload['email']!=$jsonContent[$productId]['owner_email']){


    $error=true;
}

}
catch(\Exception $exception){


    $error=true;
}
if($error){


    return responsy()->json ([  'message'=>'you ara the owner of the product,you can delet it!'],400);
}
return $next ($request , $productId);
}
}
















