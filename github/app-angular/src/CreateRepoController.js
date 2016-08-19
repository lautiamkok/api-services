app.controller('CreateRepoController', ['$scope', '$http', function($scope, $http) {

    $scope.submit = function() {
        $scope.repo = '';
        $scope.error = '';
        var encodedString = $scope.username + ':' + $scope.password;
        var data = {name:$scope.name, description:$scope.description}
        $http({
            method: 'POST',
            url: 'https://api.github.com/user/repos',
            data: JSON.stringify(data),
            headers:{
                'Authorization': "Basic " + btoa(encodedString),
                'Accept': 'application/json; odata=verbose',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(function(response) {
            $scope.repo = response.data;
            $scope.username = '';
            $scope.password = '';
            $scope.name = '';
            $scope.description = '';
        })
        .catch(function(response) {
            $scope.error = response.data;
        })
    };

}]);
