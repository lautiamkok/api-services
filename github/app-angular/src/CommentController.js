app.controller('CommentController', ['$scope', '$http', '$timeout', '$q', function($scope, $http, $timeout, $q) {

    $scope.asyncPullRquestComment = function(url) {

        var promise = $http({
            method: 'GET',
            url: url
        })
        .then (function(response) {
            var data = response.data;

            return $timeout(function() {
                return [data];
            }, 1000);
        })
        .catch(function(response) {
            var data = response.data;

            return $timeout(function() {
                return [data];
            }, 1000);
        });

        return promise;
    }

    $scope.submit = function() {
        $scope.string = '';
        $scope.emojiCollection = '';
        $scope.commentCollection = '';
        $scope.error = '';

        // Regex for emoji.
        var regex = /\:(.*?):/g;

        // Use GET /repos/:owner/:repo/pulls/:number to fetch a single pull request.
        var url = 'https://api.github.com/repos/' + $scope.owner + '/' + $scope.repo + '/pulls/' + $scope.number;

        // Use promise to make sure getting the pull request comment first, because
        // it is not included in GET /repos/:owner/:repo/issues/:number/comments
        var promise = $scope.asyncPullRquestComment(url);

        promise.then(function(success) {
            var list = success;
            var string = success[0].body

            $http({
                method: 'GET',
                url: 'https://api.github.com/repos/' + $scope.owner + '/' + $scope.repo + '/issues/' + $scope.number + '/comments'
            })
            .then(function(response) {
                var data = response.data;
                $scope.commentCollection = list.concat(data);
                $scope.owner = '';
                $scope.repo = '';
                $scope.number = '';

                // Loop through the comments and join the body string.
                data.forEach(function(comment) {
                    string += comment.body;
                });

                // Search matched emoji through the string.
                var matches = string.match(regex);

                // Remove : from emojis.
                var emojis = [];
                matches.forEach(function(emoji) {
                    emojis.push(emoji.slice(1, -1));
                });

                // Group emojis and count.
                var counts = {};

                for(var i = 0; i < emojis.length; i++) {
                    var num = emojis[i];
                    counts[num] = counts[num] ? counts[num]+1 : 1;
                }

                $scope.emojiCollection = counts;
            })
            .catch(function(response) {
                $scope.error = response.data;
            });
        }, function(error) {
            $scope.error = error;
        });
    };

}]);
