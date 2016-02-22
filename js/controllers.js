
//todo: replave Application $app with an variable $app

/**
 * User Controler
 */
angular.module('myApp').controller('UserController', ['$scope', '$log', 'UserService', 'AuthService','$cookieStore', '$location', '$rootScope', function($scope, $log, UserService, AuthService, $cookieStore, $location, $rootScope) {

    var currentResource;
    $scope.users   = {};
    $scope.addMode = true;

    $scope.resetForm = function () {
        $scope.addMode       = true;
        $scope.first_name    = undefined;
        $scope.last_name     = undefined;
        $scope.selectedIndex = undefined;
        $rootScope.message   = '';
    }

    /**
     * Get users
     */
    $scope.read = function () {
        UserService.read().then(function(response) {

            if ($scope.isAuth(response)) {
                $scope.users = response.data;
                $scope.resetForm();
            }


        });
    }
    $scope.read();

    /**
     * Get user
     */
    $scope.getUser = function() {
        UserService.getOne().then(function(response) {
            if ($scope.isAuth(response)) {
                $scope.user = response.data;
            }
        });
    }

    /**
     * @param index
     * @param userId
     * Delete user
     */
    $scope.deleteUser = function(index, userId) {
        UserService.deleteOne(userId).then(function(response) {
            if ($scope.isAuth(response)) {
                $scope.user = undefined;
                $scope.users.splice(index, 1);
            }
        });
    }

    /**
     * Create User
     */
    $scope.add = function () {

        var save = {"data": {first_name: $scope.first_name, last_name: $scope.last_name}};

        UserService.create(save).then(function(response) {
            if ($scope.isAuth(response)) {
                $scope.resetForm();
                $scope.read();
            }
        });
    };

    /**
     * Edit User
     */
    $scope.update = function () {

        var data = {first_name: $scope.first_name, last_name: $scope.last_name}
        UserService.update(currentResource.id, data).then(function(response) {

            if ($scope.isAuth(response)) {
                $scope.resetForm();
                $scope.read();
            }
        });
    }

    /**
     * @param index
     * @param id
     * Delete User
     */
    $scope.deleteUser = function (index, id) {
        UserService.deleteOne(id).then(function(response, data) {

            $scope.read();
        });
    };


    /**
     * @param index
     * Select User
     */
    $scope.selectUser = function (index) {

        currentResource   = $scope.users[index];
        $scope.addMode    = false;
        $scope.first_name = currentResource.first_name;
        $scope.last_name  = currentResource.last_name;
    }

    /**
     * Cancel (edit user)
     */
    $scope.cancel = function () {
        $scope.resetForm();
    }

    /**
     * Logout
     */
    $scope.logOut = function () {
        AuthService.logout();
    }

    /**
     * @param response
     * @returns {boolean}
     * If user is not login or his token expired, redirect to login page
     */
    $scope.isAuth = function(response) {

        if (response.auth === true) {
            return true;
        }

        $rootScope.message  = 'Token expired, please login';

        $rootScope.redirect = window.location.href;
        $location.url('/');

    }

}]);


/**
 * Login Controller
 */
angular.module('myApp').controller('LoginController', ['$scope', '$log','$cookieStore', 'AuthService', '$location', '$rootScope', function($scope, $log, $cookie, AuthService, $location, $rootScope) {

    $scope.message = ($rootScope.message) ? $rootScope.message : '';

    /**
     * Login
     */
    $scope.login = function() {
        AuthService.login($scope.auth).then(function(response) {

            if (response.data.status === 'success') {
                if (response.data.auth.token) {

                    $cookie.put('auth', response.data.auth);

                    /* redirect to users page */
                    $location.path('/crud');
                }
            /* backup validation */
            } else if (response.data.status === 'fail') {
                //todo: error message
                var message = '';
                for (i in response.data.errorMessages) {
                    message += i + ' ' + response.data.errorMessages[i] + ' ';
                }
                alert(message);
            } else {
                //todo: error message
                alert('internal error');
            }

        });
    }
}]);