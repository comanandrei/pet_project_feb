/**
 * User Service
 */
angular.module('myApp').service('UserService', ['$http', '$cookieStore', function($http, $cookie) {

    var token = $cookie.get('auth').token;

    this.read = function() {

        return $http.post('/api/users', {'token': token})
            .then(
                function(response) {
                    return response['data'];
                },
                function(err) {
                    return err;
                }
            );
    };

    this.getOne = function(id) {

         return $http.post('/api/user/' + $id, {'token': token})
               .then(
             function(response) {
                   return response['data'];
                 },
             function(err) {
                   return err;
             }
         );
     };

    this.deleteOne = function(id) {

       return $http.post('/api/delete', {'token': token, id : id})
           .then(
          function(response) {
                return response['data'];
              },
          function(err) {
                return err;
              }
        );
       };


    this.update = function(id,data) {
            data['token'] = token;
            return $http.put('/api/update/' +id, data)
                .then(
                    function(response) {
                        return response['data'];
                    },
                    function(err) {
                        return err;
                    }
                );
    };


    this.create = function(data) {
        data['token'] = token;
        return $http.post('/api/create', data)
            .then(
                function(response) {
                    return response['data'];
                },
                function(err) {
                    return err;
                }
            );
    };

}]);


/**
 * Auth Service
 */
angular.module('myApp').service('AuthService', ['$http', '$rootScope', '$location', function($http, $rootScope, $location) {


    this.login = function(data) {

        return $http.post('/api/auth/login', data)
            .then(
                function(response) {
                    return response;
                },
                function(err) {
                    return err;
                }
                );
    };

    this.logout = function() {

        return $http.get('/api/auth/logout')
            .then(
                function(response) {
                    $rootScope.message  = '';

                    $rootScope.redirect = window.location.href;
                    $location.url('/');
                },
                function(err) {
                    return err;
                }
            );
    };


}]);