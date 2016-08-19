app.controller('GetReposController', ['$scope', '$http', function($scope, $http) {

    $scope.submit = function() {
        $scope.collection = '';
        $scope.error = '';
        var encodedString = $scope.username + ':' + $scope.password;
        $http({
            method: 'GET',
            url: 'https://api.github.com/user/repos',
            headers:{
                'Authorization': "Basic " + btoa(encodedString),
                'Accept': 'application/json; odata=verbose',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(function(response) {
            $scope.collection = response.data;
            $scope.username = '';
            $scope.password = '';
        })
        .catch(function(response) {
            $scope.error = response.data;
        })
    };

}]);
