app.controller('PullRequestController', ['$scope', '$http', function($scope, $http) {

    $scope.submit = function() {
        $scope.collection = '';
        $scope.error = '';
        $http({
            method: 'GET',
            url: 'https://api.github.com/repos/' + $scope.owner + '/' + $scope.repo + '/pulls'
        })
        .then(function(response) {
            $scope.collection = response.data;
            $scope.owner = '';
            $scope.repo = '';
        })
        .catch(function(response) {
            $scope.error = response.data;
        })
    };

}]);
