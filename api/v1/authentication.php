<?php 
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    echoResponse(200, $session);
});



$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'),$r->user);
    $response = array();
    $db = new DbHandler();
    $password = $r->user->password;
    $email = $r->user->email;
    $user = $db->getOneRecord("select uid,name,password,email,created from users_auth where email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
        $response['status'] = "success";
        $response['message'] = 'Inicio de sesion satisfactorio.';
        $response['name'] = $user['name'];
        $response['uid'] = $user['uid'];
        $response['email'] = $user['email'];
        $response['createdAt'] = $user['created'];
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['uid'] = $user['uid'];
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $user['name'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Inicio de sesion fallido. Las credenciales no son correctas.';
        }
    }else {
            $response['status'] = "error";
            $response['message'] = 'El usuario no esta registrado.';
        }
    echoResponse(200, $response);
});

function name_validate($name)
{
    $flag = false;
    for($i=0;$i<strlen($name);$i++)
        if($name[$i]==' ')
            $flag = true;
    if(!$flag)
        return false;
    $name = explode(" ", $name);
    if (strlen($name[0])<2 || strlen($name[1])<2) 
        return false;
    return true;
}

function password_equals($p1,$p2)
{
    if (strlen($p2)==0)
        return false;
    return $p1==$p2;
}

function password_validate($pass)
 {
    if(strlen($pass)<6)
        return false;
    $counter = 0;
    for ( $i=0 ; $i<strlen($pass) ; $i++)
    {
        if(ctype_upper($pass[$i]))
            $counter = $counter+1;
        if(is_numeric($pass[$i]))
            $counter = $counter+1;
        if($pass[$i]=='.' || $pass[$i]=='!' ||$pass[$i]=='?')
             $counter = $counter+1;
    }
    if ($counter<3)
        return false;
    return true;

 }
function luhn_validate($s) {
  if(0==$s) { return(false); } // Don't allow all zeros
  $sum=0;
  $i=strlen($s);     // Find the last character
  while ($i-- > 0) { // Iterate all digits backwards
    $sum+=$s[$i];    // Add the current digit
    // If the digit is even, add it again. Adjust for digits 10+ by subtracting 9.
    (0==($i%2)) ? ($s[$i] > 4) ? ($sum+=($s[$i]-9)) : ($sum+=$s[$i]) : false;
  }     
  return (0==($sum%10)) ;
} 
function cod_validate($cod)
{
    return (strlen($cod)>=2 && strlen($cod)<=4);
}
function email_validate($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function date_validate($date)
{
    $current_date = new DateTime();
    $time = strtotime($date);
    $newformat = date('m-d-Y',$time);
    return true;
}
function validaciones($password,$password2,$name,$tarjeta,$cod,$email,$date)
{
     if (!password_validate($password))
    {
        $response["status"] = "error";
        $response["message"] = "Contraseña debe tener por lo menos 6 caracteres y tres de cuatro grupos de caracteres (mayúsculas, minúsculas, signos de puntuación, números)";
        echoResponse(201, $response);
        return false;
    }
    if (!password_equals($password,$password2))
    {
        $response["status"] = "error";
        $response["message"] = "Una de las contraseñas esta vacia y no son iguales.";
        echoResponse(201, $response);
        return false;
    }
    if (!name_validate($name))
    {
        $response["status"] = "error";
        $response["message"] = "Nombre y Apellido mínimamente. 2 caracteres cada uno como mínimo.";
        echoResponse(201, $response);
        return false;
    }
    if (!luhn_validate($tarjeta))
    {
        $response["status"] = "error";
        $response["message"] = "La tarjeta no es una tarjeta valida.";
        echoResponse(201, $response);
        return false;
    }
     if (!cod_validate($cod))
    {
        $response["status"] = "error";
        $response["message"] = "Código de seguridad, mínimo 2 dígitos, máximo 4.";
        echoResponse(201, $response);
        return false;
    }
     if (!email_validate($email))
    {
        $response["status"] = "error";
        $response["message"] = "Email invalido.";
        echoResponse(201, $response);
        return false;
    }
     if (!date_validate($date))
    {
        $response["status"] = "error";
        $response["message"] = "La fecha es pasada al dia de hoy.";
        echoResponse(201, $response);
        return false;
    }
    return true;
}

$app->post('/signUp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'name', 'password'),$r->user);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $name = $r->user->name;
    $email = $r->user->email;
    $password = $r->user->password;
    $tarj = $r->user->tarj;
    $password2 = $r->user->password2;
    $cod = $r->user->cod;
    $date = $r->user->date;
    $isUserExists = $db->getOneRecord("select 1 from users_auth where email='$email'");
    if(!$isUserExists)
    {
        if (validaciones($password,$password2,$name,$tarj,$cod,$email,$date))
        { 
            $r->user->password = passwordHash::hash($password);
            $tabble_name = "users_auth";
            $column_names = array( 'name', 'email', 'password');
            
        
            $result = $db->insertIntoTable($r->user, $column_names, $tabble_name);      
            if ($result != NULL) 
            {
                $response["status"] = "success";
                $response["message"] = "El usuario fue creado correctamente.";
                $response["uid"] = $result;
                if (!isset($_SESSION)) 
                {
                    session_start();
                }
                $_SESSION['uid'] = $response["uid"];
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                echoResponse(200, $response);
            } 
            else 
            {
                $response["status"] = "error";
                $response["message"] = "Error al crear usuario. Intente otra vez.";
                echoResponse(201, $response);
            }
        }
        
    }  
    else
    {
        $response["status"] = "error";
        $response["message"] = "Un usuario con el mismo correo ya existe.";
        echoResponse(201, $response);
    }
});

$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Cierre de sesion satisfactorio.";
    echoResponse(200, $response);
});
?>