describe('CreateRepoController Test', function() {

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
        controller("CreateRepoController", {$scope: scope, $httpBackend: httpBackend});
    }));
    // ...Boilerplate ends here

    // Our tests will go here.
    it('should populate $scope.repo using $when (201 status)', function() {

        scope.username = 'splllctre';
        scope.password = 'sp8ctre';
        scope.name = 'Hello World';
        scope.description = 'Anything you want to write';

        scope.submit();

        var encodedString = scope.username + ':' + scope.password;
        var data = {name:scope.name, description:scope.description}

        httpBackend.whenPOST('https://api.github.com/user/repos', data, {
            "Authorization": "Basic " + btoa(encodedString),
            "Accept": "application/json; odata=verbose",
            "Content-Type": "application/x-www-form-urlencoded"
        }).respond(function(){ return [200,{foo: 'bar'}]});

        httpBackend.flush();

        expect(scope.repo).toEqual({foo: 'bar'});

    });
});
