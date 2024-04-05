# Implementationof 2-Factor Authentication with TOTP

TOTP are Time-based One-Time-Passkeys.
These are 6-digit combinations that are generated and validated by standardized algorithms defined in the Internet Engineering Task Force (IETF) standard RFC 6238.
TOTP are widely adopted as a 2FA (2-Factor Authentication) mechanism to validate that a user possesses a device configured with the correct **secret** and capable of generating the correct numerical sequences.
Possession of such a device constitutes a second factor in authentication, hence the term 2FA.

There are other means of authenticating with a second fact, but this discussion will only concern TOTP-based 2FA.

## The Process At-A-Glance

The process of setting up TOTP-based 2FA is essentially as follows:
1. A user which is logged in or currently registering is presented with a QR code to scan with their Authenticator app. This QR code contains the **label**, **identity** of the issuer, and a random **secret**. This QR code is presented only once and never again after the setup process. Scanning the QR code with Google Authenticator will add a record to produce codes every 30 seconds. On the server side, we keep the secret in a session variable for now.
2. The user must input their current TOTP code for the new authenticator record and submit that form. This serves as proof that the configuration is accepted by their Authenticator app.
3. The Web app (on the server side) receives the code and validates it against the secret from the session. If it comes back valid, then we can save the secret from the session for this user in their user account table record (in a dedicated field).

Thereafter, the login process must be modified:
1. If the user record contains a secret and the correct password was given, then, in a second step we request the 6-digit code to validate their login attempt. The user inputs it and submits.
2. The TOTP code is validated against the secret. If it comes back valid, then the user is logged in, otherwise not.

In the following sections, we implement the scheme.

## Including Libraries

Reinventing the wheel would be pointless... we will use freely available implementations.

GitHub user chillerlan distributes his `php-qrcode` and `php-authenticator` projects on [Packagist](https://packagist.org/).
To include them in our project, we run 2 `composer require` commands, as follows:
```
composer require chillerlan/php-authenticator
composer require chillerlan/php-qrcode
```

To ensure that the code is usable by our project, we ensure that we autoload the dependencies by requiring the composer autoloader as follows:
```
require_once('vendor/autoload.php');
```
This line must run for all requests and therefore it should be placed in your file handling the inclusion of all dependencies.
In our case, this file should be `app/core/init.php`.

## Generating and Communicating the Secret

Consider the addition of a route from `/User/setup2fa` to the `User` Controller class' `setup2fa` method such as follows in the `routes.php` file:
```
$this->addRoute('User/setup2fa' , 'User,setup2fa');
```

The first step in setting up the 2FA secret with a user is to create a secret and store it in a SESSION variable.
This can be done with the `chillerlan\Authenticator\{Authenticator, AuthenticatorOptions}` class objects with a secret length of 32 as follows:
```
use chillerlan\Authenticator\{Authenticator, AuthenticatorOptions};
use chillerlan\QRCode\QRCode;
...
$options = new AuthenticatorOptions();
$authenticator = new Authenticator($options);
...
$_SESSION['secret_setup'] = $authenticator->createSecret();
```

Next, we wish to generate the correct URI to instruct the Authenticator app of the encoded information.
The format for this is as follows
```
otpauth://TYPE/LABEL?PARAMETERS
```
and for example
```
otpauth://totp/Example:alice@google.com?secret=JBSWY3DPEHPK3PXP&issuer=Example
```
This is implemented with the Authenticator class `getUri` method as follows:
```
$uri = $authenticator->getUri('AccountLabel', 'IssuerIdentity');
```
Finally, we generate a QR code with the `chillerlan\QRCode\QRCode` class object render method and pass it to a view as follows:
```
$QRCode = (new QRCode)->render($uri);
$this->view('User/setup2fa',['QRCode'=>$QRCode]);
```
This QR code will contain all the information needed for an authenticator to configure a record and generate TOTPs matching the configuration.

## Displaying the QR Code

The `User/setup2fa` view must present the QR code and include a form to submit a TOTP matching the new authenticator configuration:
```
<html>
<head>
<title>2fa set up</title>
</head>
<body>
<img height=300 width=300 src="<?= $QRCode ?>">
Scan the above QR-code with your mobile Authenticator app, such as Google Authenticator. The authenticator app will generate codes that are valid for 30 seconds only. Enter such a code and submit it while it is 
still valid to apply the 2-factor authentication protection to your account.
<form method="post" action="">
<label>Current code:<input type="text" name="totp" 
/></label>
<input type="submit" name="action" value="Verify code" />
</form>
</body>
</html>
```
In this view we see that the QR Code is not in a file generated and saved to then be served but rather fed directly an an encoded image through the image src attribute.
We also notice a form allowing a user to confirm the TOTP record setup in their authenticator app.

## Confirming the Secret

To confirm the secret, we must confirm the user Authenticator app can generate appropriate TOTPs matching the secret.

We must start by generating the Authenticator object with the same options as in the setup phase, as follows:
```
use chillerlan\Authenticator\{Authenticator, AuthenticatorOptions};
use chillerlan\QRCode\QRCode;
//...
$options = new AuthenticatorOptions();
$authenticator = new Authenticator($options);
```
Next, in the case where the user submitted the form, we use the SESSION secret to further configure the `$authenticator` object and reject any request for which this session variable is unavailable.
```
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_SESSION['secret_setup'])){
    $authenticator->setSecret($_SESSION['secret_setup']);
  }else{
    header('location:/User/setup2fa');
  }
```
We are ready to validate the user-provided TOTP (6-digit code) against the session secret.
This is done with the `Authenticator` class `verify` method as follows: 
```
  //was submitted, check the TOTP
  $totp = $_POST['totp'];
  if($authenticator->verify($totp)){
    //record to the user record
    echo 'yay!';
  }else{
    echo 'Nope!';
  }
}
```
There are two possible outcomes: true, the 6-digit code provided by the user is correct; false, the code does not match the secret.

## Putting it all Together

With the provided view above, the `setup2fa` method within the `User` controller class could resemble the following: 
```
namespace app\controllers;

use chillerlan\Authenticator\{Authenticator, AuthenticatorOptions};
use chillerlan\QRCode\QRCode;

class User extends \app\core\Controller{
//...	
	function setup2fa(){
		$options = new AuthenticatorOptions();
		$authenticator = new Authenticator($options);

		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			if(isset($_SESSION['secret_setup'])){
				$authenticator->setSecret($_SESSION['secret_setup']);
			}else{
				header('location:/User/setup2fa');
			}
			//was submitted, check the TOTP
			$totp = $_POST['totp'];
			if($authenticator->verify($totp)){
				//record to the user record
				echo 'yay!';
			}else{
				echo 'Nope!';
			}
		}else{
			$_SESSION['secret_setup'] = $authenticator->createSecret();
			//generate the URI with the secret for the user
			$uri = $authenticator->getUri('Superb application', 'localhost');
			$QRCode = (new QRCode)->render($uri);
			$this->view('User/setup2fa',['QRCode'=>$QRCode]);
		}
	}
//...
}
```

## Persistence

For any user who confirms their TOTP secret, this secret shouild be recorded to their `user` table record.
Therefore, we must add a `secret` NULLABLE field of sufficient length, say the default length of 32, to the `user` table with NULL as its default value.
This way, we can log users with and without 2FA differently in the system.

Consider the new model method as follows:
```
namespace app\models;

use PDO;

class User extends \app\core\Model{
//...
	public function add2FA(){
		//change anything but the PK
		$SQL = 'UPDATE user SET secret = :secret WHERE user_id = :user_id';
		$STMT = self::$_conn->prepare($SQL);
		$STMT->execute(['user_id'=>$this->user_id,
						'secret'=>$this->secret]);
	}
//...
}
```
This method can be invoked on a user record to save the new secret as follows:
```
$user = new \app\models\User();
$user = $user->getById($_SESSION['user_id']);//or $user->user_id = $_SESSION['user_id']; if the record will not be used further
$user->secret=$_SESSION['secret_setup'];
$user->add2FA();
```
## Login process

To adjust the login process to require TOTP input from the user, we can apply the following changes.

In the login function, upon successful login, add the user secret to session variables as follows:
```
$_SESSION['secret'] = $user->secret;
```

Modify the Login access filter to redirect when a user has a secret in their record:
```
public function redirected(){
		//make sure that the user is logged in
		if(!isset($_SESSION['user_id'])){
			header('location:/User/login');
			return true;
		}
		if($_SESSION['secret']!=NULL){
			header('location:/User/check2fa');
			return true;
		}
		return false;//not denied
	}
```
Here, we redirect to `/User/check2fa` when the user has a secret in their account.

Add the route for `/User/check2fa` as follows:
```
$this->addRoute('User/check2fa' , 'User,check2fa');
```

Create a `User/check2fa` view to allow the user to send in their 6-digit TOTP code:
```
<html>
<head>
<title>2fa set up</title>
</head>
<body>
<p>Submit the 6-digit code for this site from your Authenticator app.</p>
<form method="post" action="">
<label>Current code:<input type="text" name="totp" 
/></label>
<input type="submit" name="action" value="Verify code" />
</form>
</body>
</html>
```

Create a method `check2fa` in the `User` controller class as follows:
```

```
