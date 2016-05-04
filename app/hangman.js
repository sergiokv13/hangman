 app.controller("controladorPrincipal", ["$scope", "$http", function($scope, $http) {
      $scope.letter = '';
      $scope.health = 6;
      $scope.revealed = "";
      $scope.hasWon = false;
  
  $scope.hasFinished = function() { 
     return $scope.health == 0 || $scope.hasWon;
  };
  
  $scope.init = function() {
     $http.post('partials/play.php').then( processResponse );
  };
  
  $scope.submitLetter = function() {
     var data = { letter: $scope.letter };
     $http.post('partials/play.php', data).then(processResponse);
     $scope.letter = '';
  };
  
  $scope.init();
  
  function processResponse(response) {
    $scope.hasWon = response.data.hasWon;
    $scope.health = response.data.health;
    $scope.revealed = response.data.revealed;
  }

}]);