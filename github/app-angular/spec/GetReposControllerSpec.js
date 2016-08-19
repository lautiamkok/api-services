describe('GetReposController Test', function() {

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
        controller("GetReposController", {$scope: scope, $httpBackend: httpBackend});
    }));
    // ...Boilerplate ends here

    // Our tests will go here.
    it('should populate $scope.collection using $when (200 status)', function() {

        scope.username = 'splllctre';
        scope.password = 'sp8ctre';

        scope.submit();

        var encodedString = scope.username + ':' + scope.password;

        httpBackend.whenGET('https://api.github.com/user/repos', undefined, {
            Authorization: "Basic " + btoa(encodedString),
            Accept: "application/json;odata=verbose",
            ContentType: "application/x-www-form-urlencoded"
        }).respond(function(){ return [200,{foo: 'bar'}]});

        httpBackend.flush();

        expect(scope.collection).toEqual({foo: 'bar'});

    });
});
