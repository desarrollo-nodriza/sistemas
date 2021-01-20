<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Firebase', array('file' => 'Firebase/vendor/autoload.php'));

use Kreait\Firebase\Factory;
use Firebase\Auth\Token\Exception\InvalidToken;

class FirebaseComponent extends Component
{	

    private static $factory;
    private static $credential = APP . 'Vendor' . DS . 'Firebase' . DS . 'credentials' . DS . 'sistema-nodriza-firebase-adminsdk-rrtbw-e69f6fa6f8.json';

    public function initialize(Controller $controller)
    {   
        $this::$factory = (new Factory)->withServiceAccount($this::$credential);
    }


    /**
     * Valida que el token sea válido en google
     * 
     * @param varchar $token  Token de google
     * @return array
     */
    public function isLogged($token)
    {   
        $factory = (new Factory)->withServiceAccount($this::$credential);
        $auth = $factory->createAuth();

        $error = '';

        try 
        {
            $verifiedIdToken = $auth->verifyIdToken($token);
        } 
        catch (InvalidToken $e) 
        {
            $error = 'El token de google ingresado no es válido: ' . $e->getMessage();
        } 
        catch (\InvalidArgumentException $e) 
        {
            $error = 'El formato de google del token no es válido: ' . $e->getMessage();
        }
        
        # En caso de error retornamos
        if ($error)
        {
            return array(
                'logged' => 0,
                'user' => array(),
                'message' => $error
            );
        }

        $uid = $verifiedIdToken->claims()->get('sub');
        
        $user = $auth->getUser($uid);

        return array(
            'logged' => 1,
            'user' => array(
                'uid' => $user->uid,
                'email' => $user->email,
                'emailVerified' => $user->emailVerified,
                'displayName' => $user->displayName,
                'photoUrl' => $user->photoUrl,
                'phoneNumber' => $user->phoneNumber,
                'disabled' => $user->disabled
            ),
            'message' => 'Token de google validado con éxito.'
        );
    }
}