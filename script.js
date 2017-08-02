var app=angular.module('mainapp',['ngRoute']);

app.config(function($routeProvider){
	$routeProvider
	.when('/', {
		templateUrl:'login_new.html',
		controller:'loginController'
	})
	.when('/welcome',{
		templateUrl: 'welcome.html',
		controller: 'welcomeController'
	});
	$routeProvider.when('/logout',{
		template: "this is logout page"
	});
});

app.controller('loginController',['$scope','$rootScope', '$http', '$location','$sce', function($scope,$rootScope,$http,$location,$sce){
	$scope.loginFrm=true;
	$scope.login= function(){
		$http({
            method: 'POST',
            url: 'test.php',
            data: {
            	username: $scope.username,
            	password: $scope.password,
            	action: 'userAuthenticate'
            },
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(success){
			$scope.http_response=$sce.trustAsHtml(success.data);
			$location.path('/welcome');
		}, function(err){
			console.log(err);
		});
	}
}]);

app.controller('welcomeController', ['$scope','$rootScope', '$http', '$location','$sce', function($scope,$rootScope,$http,$location,$sce){
	$scope.loginFrm=false;
	$scope.addStudent=false;
	$scope.monitorDetails=true;
	$scope.details=false;
	$scope.addButton=true;
	$scope.marksInfo=false;
	$scope.AddStudent= function(){
		$scope.addStudent = !$scope.addStudent;
	}
	$scope.pullCourse= function(){
		$scope.studentInfo=false;
		$http({
	    	method: 'POST',
	       	url: 'test.php',
	        data: {
	        	classname: $scope.classSelect,
	        	action:'courseSelection'
	        },
		  	headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	    }).then(function(success){
			$scope.courseList=success.data;
			$rootScope.list=$scope.courseList;
			$rootScope.idList = {
				course1: $rootScope.list[0].course_id,
				course2: $rootScope.list[1].course_id,
				course3: $rootScope.list[2].course_id
			};
			$scope.studentInfo=true;
		}, function(err){
			console.log(err);
		});
	}

	$scope.InsertStudent= function(){
		$scope.addStudent=false;
		$http({
            method: 'POST',
            url: 'test.php',
            data: {
            	username: $scope.userName,
            	password: $scope.pwd,
            	firstname: $scope.firstname,
            	lastname: $scope.lastname,
            	classname: $scope.classSelect,
            	marks1: $scope.course1,
            	marks2: $scope.course2,
            	marks3: $scope.course3,
            	course1: $rootScope.idList.course1,
            	course2: $rootScope.idList.course2,
            	course3: $rootScope.idList.course3,
            	action: 'studentAddition'
            },
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(success){
			$scope.http_response=$sce.trustAsHtml(success.data);
		}, function(err){
			console.log(err);
		});
	}

	$scope.showStudent= function(){
		$scope.details = !$scope.details;
	}

	$scope.pullStudent= function(){
		$http({
	    	method: 'POST',
	       	url: 'test.php',
	        data: {
	        	classname: $scope.classSelection,
	        	action:'studentSelection'
	        },
		  	headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	    }).then(function(success){
	    	$scope.studentnames=true;
			$scope.studentlist=success.data;
			$rootScope.slist=$scope.studentlist;
			//console.log($scope.studentlist[0].student_name);
		}, function(err){
			console.log(err);
		});
	}

	$scope.pullDetails= function(){
		$http({
	    	method: 'POST',
	       	url: 'test.php',
	        data: {
	        	student: $scope.studentselect,
	        	action:'studentdetails'
	        },
		  	headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	    }).then(function(success){
	    	$scope.studentinfo=success.data;
			$rootScope.info=$scope.studentinfo;
			$scope.marksInfo=true;
		}, function(err){
			console.log(err);
		});
	}
}]);
