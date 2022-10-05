$referer = request()->headers->get('referer'); // get url E.g. http://127.0.0.1:8000/SMS/index/6727359
$urlArray = explode("/", $referer); // split 
$siteId =   end($urlArray); // get the last value, that would be siteId
