/**
 * Routes
 */
angular.module('myApp').config(function($stateProvider, $urlRouterProvider) {
    $stateProvider.state('login', {
        url: '/',
        controller: 'LoginController',
        templateUrl: 'pages/login.html'
    }).state('users', {
        url: '/crud',
        controller: 'UserController',
        templateUrl: 'pages/users.html'
    });

    /* default route */
    $urlRouterProvider.otherwise('/');
});
