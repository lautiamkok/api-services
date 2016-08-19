describe('PullRequestController Test', function() {

    // Boilerplate starts from here...
    // Load the module with MainController
    beforeEach(module('RepoApp'));

    var controller, scope, httpBackend;

    // inject the $controller and $rootScope services.
    // in the beforeEach block.
    beforeEach(inject(function ($rootScope, $controller, $httpBackend) {

        // Create a new scope that's a child of the $rootScope.
        scope = $rootScope.$new();

        httpBackend = $httpBackend;

        // Create the controller.
        controller = $controller;
        controller("PullRequestController", {$scope: scope, $httpBackend: httpBackend});
    }));
    // ...Boilerplate ends here

    // Our tests will go here.
    it('should populate $scope.collection using $when (200 status)', function() {

        scope.owner = 'splllctre';
        scope.repo = 'Hello-World';

        scope.submit();

        // Must insert the url manually in otherwise the test will fail.
        httpBackend.whenGET('https://api.github.com/repos/splllctre/Hello-World/pulls', undefined, {})
        .respond(function(){ return [200,{foo: 'bar'}]});

        httpBackend.flush();

        expect(scope.collection).toEqual({foo: 'bar'});

    });
});
