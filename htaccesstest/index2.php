<?PHP
print_r($_SERVER);

$method = $_SERVER['REQUEST_METHOD']; //e.g. GET, POST
$path_info = $_SERVER['REQUEST_URI']; //e.g. index.php/authors/
$uri_parts = parse_uri($path_info);
$resource_type = $uri_parts['resource_type']; //e.g. "authors"
$request = $uri_parts['request']; //anything after the resource type

print "test: $method,$resource_type,$request";


function parse_uri($path_string)
{
  // $path_string is something like
  // 'index.php/authors/'
  $path_parts = explode("/",$path_string);
  $restype = $path_parts[1];
  $req = $path_parts[2];

  $ret_array = array();
  $ret_array['resource_type'] = $restype;
  $ret_array['request'] = $req;

  return $ret_array;
}

?>