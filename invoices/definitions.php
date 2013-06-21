function setRegex(){
	$regex['nameregex'] = "/[a-zA-Z,'- ]+/";
	$regex['emailregex']="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2.4}|museum)$/";
	return $regex;
}
function setWorkers(){
	global $regex;
	$nameregex=$regex['nameregex'];
	$emailregex=$regex['emailregex'];

	$workers[0]['name']='workers';
	$workers[0]['fields']['id']= array ('title'=>'IdNo','edit'=>'0');
	$workers[0]['fields']['name']= array ('title'=>'Name','edit'=>'1','required'=>'1','validation'=>$nameregex,'type'=>'text');
	$workers[0]['fields']['companyid']= array ('foreignkey'=>'1','title'=>'Company','edit'=>'1','type'=>'pulldown','foreigntableno'=>1,'foreignfield'=>'companies.name','required'=>'1');
	$workers[0]['fields']['manager']= array ('title'=>'Manager','edit'=>'1','required'=>'0', 'validation'=>$nameregex);
	$workers[0]['fields']['email']= array ('title'=>'Email','edit'=>'1','validation'=>$emailregex,'type'=>'text');
	$workers[0]['fields']['active']= array ('title'=>'Active','edit'=>'1','validation'=>"/[10]/",'required'=>1,'type'=>'text');
	$workers[0]['fields']['timestamp']= array ('title'=>'IdNo','edit'=>'0');

	$workers[0]['index'] = 'id';
	$workers[0]['filter']= 'active=active';
	$workers[0]['sort']='order by name asc';

	$workers[1]['name']= 'companies';
	$workers[1]['join']='workers.companyid=companies.id';
	return $workers;
}