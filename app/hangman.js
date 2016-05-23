
 app.controller("controladorPrincipal", ['$rootScope',"$scope", "$http", function($rootScope,$scope, $http) {
      $scope.letter = '';
      $scope.health = 6;
      $scope.revealed = "";
      $scope.hasWon = false;
      $scope.uidModel = $rootScope.uid

  $scope.hasFinished = function() { 
     return $scope.health == 0 || $scope.hasWon;
  };
  
  $scope.init = function() {
     $http.post('partials/play.php').then( processResponse );
  };
  
  $scope.submitLetter = function() {
     var data = { 
        'letter' : $scope.letter ,
        'uidModel' : $rootScope.uid
      };
     $http.post('partials/play.php', data).then(processResponse);
     $scope.letter = '';
     $scope.uidModel = $rootScope.uid
  };
  
  $scope.init();
  
  function processResponse(response) {
    $scope.hasWon = response.data.hasWon;
    $scope.health = response.data.health;
    $scope.revealed = response.data.revealed;
  }

}]);