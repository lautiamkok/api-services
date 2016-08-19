describe('CommentController Test', function() {

    beforeEach(module('RepoApp'));

    var controller, scope, httpBackend, deferred, timeout;

    beforeEach(inject(function ($rootScope, $controller, $httpBackend, $timeout, $q) {

        // Create a new scope that's a child of the $rootScope.
        scope = $rootScope.$new();

        httpBackend = $httpBackend;

        var parent = {
            body: "Nullam quis ante. :+1: cursus nunc :100: ."
        };

        var children = [
            {
                body: "Phasellus :+1: augue. Curabitur ullamcorper :100: ultricies nisi. :8ball: Etiam rhoncus."
            },
            {
                body: "Etiam :+1: ultricies nisi vel augue. Curabitur ullamcorper :-1:"
            }
        ];

        httpBackend
            .whenGET('https://api.github.com/repos/splllctre/Hello-World/pulls/2', undefined, {})
            .respond(function(){ return [200, parent]});

        httpBackend
            .whenGET('https://api.github.com/repos/splllctre/Hello-World/issues/2/comments', undefined, {})
            .respond(function(){ return [200, children]});

        deferred = $q.defer();

        timeout = $timeout;

        // Create the controller.
        controller = $controller;
        controller("CommentController", {$scope: scope, $httpBackend: httpBackend, $timeout: timeout});
    }));

    it('should count emojis and populate $scope.emojiCollection using $when (200 status)', function() {

        scope.owner = 'splllctre';
        scope.repo = 'Hello-World';
        scope.number = '2';

        scope.submit();

        // Wait for backend return parent - /repos/splllctre/Hello-World/pulls/2
        httpBackend.flush();

        // Wait for timeout in scope.parent.
        timeout.flush();

        // Wait for backend return children - repos/splllctre/Hello-World/issues/2/comments
        httpBackend.flush();

        var counts = {"100": 2, "+1": 3, "8ball": 1, "-1": 1};

        expect(scope.emojiCollection).toEqual(counts);
    });

});


