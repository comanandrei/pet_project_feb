<?php

namespace User;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package User
 */
class UserController
{

    private $isLoggendIn = false;

    public function __construct()
    {
        session_start();

        $request = new Request ();
        $this->resetToken($request);

    }


    /**
     * @param Application $app
     * @return JsonResponse
     * Get All Users
     */
    public function getAllAction(Application $app)
    {
        if ($this->isLoggendIn) {
            return new JsonResponse(array('data' => $app['db']->fetchAll("SELECT * FROM users"),
                                           'auth' => true));
        }
        return $this->redirectLogin();
    }

    /**
     * @param $id
     * @param Application $app
     * @return JsonResponse
     * Get User
     */
    public function getOneAction($id, Application $app)
     {
         if ($this->isLoggendIn) {
             return new JsonResponse(array('data' => $app['db']->fetchAssoc("SELECT * FROM users WHERE id=:ID", ['ID' => $id]),
                                            'auth' => true));
         }
         return $this->redirectLogin();
     }


    /**
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     * Delete User
     */
    public function deleteOneAction(Request $request, Application $app)
     {

         if ($this->isLoggendIn) {
             $request = json_decode($request->getContent());
             $app['db']->delete('users', ['ID' => $request->id]);
             return new JsonResponse(array('auth' => true));
         }
         return $this->redirectLogin();
     }


    /**
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * Create User
     */
     public function addOneAction(Application $app, Request $request)
     {
         if ($this->isLoggendIn) {
            $request = json_decode($request->getContent());

            $newResource = [
              'id'      => (integer)$app['db']
                          ->fetchColumn("SELECT max(id) FROM users") + 1,
             'first_name'  => $request->data->first_name,
             'last_name' => $request->data->last_name,
            ];
             $app['db']->insert('users', $newResource);

             return new JsonResponse(array('data' => $newResource,
                                        'auth' => true));
         }
         return $this->redirectLogin();
     }

    /**
     * @param $id
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * Edit User
     */
    public function editOneAction($id, Application $app, Request $request)
    {
        if ($this->isLoggendIn) {
            $request = json_decode($request->getContent());

            $resource = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
            ];
            $app['db']->update('users', $resource, ['id' => $id]);

            return new JsonResponse(array('data' => $resource,
                'auth' => true));
        }
        return $this->redirectLogin();
    }


    /**
     * @param Request $request
     * @return bool
     * Reset token (reset token if it is not expired)
     */
    private function resetToken(Request $request) {

        $request = json_decode($request->getContent(), 1);

        if ($_SESSION["auth"] && $_SESSION["auth"]["token"] && $_SESSION["auth"]["token"]["key"] === $request["token"] && (time() - EXPIRATION_TOKEN) <= $_SESSION["auth"]["token"]["expirationTime"]) {

            /* reset the token */
            $_SESSION["auth"]["token"]["expirationTime"] = time() + EXPIRATION_TOKEN;
            $this->isLoggendIn = true;
            return true;
        }
        $this->isLoggendIn = false;
        return false;
    }

    /**
     * @return JsonResponse
     * Redirect to login page
     */
    private function redirectLogin() {
        return new JsonResponse(array('auth' => false));
    }
}