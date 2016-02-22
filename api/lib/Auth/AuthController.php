<?php

namespace Auth;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class AuthController
 * @package Auth
 */
class AuthController
{


    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * Login
     */
    public function loginAction(Application $app, Request $request) {


        $credentials = json_decode($request->getContent(), 1);

        /* if "credentials" is empty, initialize it with empty field for validation */
        if (empty($credentials)) {
            $credentials = array('user' => '', 'password' => '');
        }

        $constraint = new Assert\Collection(array(
            'user'     =>  array(new Assert\NotBlank(), new Assert\Length(array('min' => 4))),
            'password' =>  array(new Assert\NotBlank(), new Assert\Length(array('min' => 6)))
        ));


        $errors = $app['validator']->validateValue($credentials, $constraint);

        /* validation errors*/
        if (count($errors) > 0) {

            $errorMessages = array();
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse([ "status"        => "fail",
                                      "errorMessages" => $errorMessages  ]);
        } else {

            if($auth = $this->verifyCredentials($app,$credentials)) {
                $token            = $this->createToken($auth);

                /* save user and token in session */
                $_SESSION["auth"] = $authResponse = array(
                    'user' => array(
                        'id'       => $auth['id'],
                        'username' => $auth['username']
                    ),
                    'token' => array(
                        'key'            => $token,
                        'expirationTime' => time() + EXPIRATION_TOKEN       //5min
                    )
                );

                return new JsonResponse(array(
                    'status' => 'success',
                    'auth'   => array(
                        'token' => $token,
                        'user'  => array(
                            'username' =>$auth['username']
                        )
                    )

                ));
            }

            return new JsonResponse(["status" => "Incorrect Credentials"]);
        }

    }

    /**
     * @return JsonResponse
     * Logout
     */
    public function logoutAction() {

        $_SESSION["auth"] = array();
        session_destroy();

        return new JsonResponse(["auth"=> "fail"]);
    }

        private function createToken($auth) {
        return md5($auth['username']);
    }

    /**
     * @param $password
     * @return string
     * Encrypt the password
     */
    private function hashPassword($password) {

        return hash('sha256', $password);
    }

    /**
     * @param $app
     * @param $credentials
     * @return mixed
     * Verify if credentials are correct
     */
    private function verifyCredentials($app, $credentials) {

       return $app['db']
            ->fetchAssoc("SELECT * FROM auth WHERE username=:UserName AND password=:Password", ['UserName' => $credentials["user"], 'Password' => $this->hashPassword($credentials["password"])]);

    }
}