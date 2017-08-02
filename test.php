<?php
	session_start();
    class studentService {

        public static $connection;

        public function __construct() {
            $this->connection = mysqli_connect('localhost', 'root','', 'marlabstest');
        }

        function __destruct() {
            $this->connection->close();
        }

        public function userAuthenticate($username, $password){
            if($this->connection->connect_error){
                die('Could not connect: ' . mysql_error());
            }else {
                if($username=='' || $password=='') {
                    echo 'Please enter login details';
                    http_response_code(406);
                } else {
                    $sql = 'SELECT student_id from student where username = \''.$username.'\' AND password = \''.$password.'\'';
                    $result = $this->connection->query($sql);
                    $a_user = '';
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $a_user = $row;
                        }
                        $_SESSION['username'] = $a_user['username'];
                        $_SESSION['password'] = $a_user['password'];
                    }else{
                        echo 'Incorrect credentials';
                        http_response_code(406);
                    }
                }
            }   
        }

        public function courseSelection($classname){
            if($classname==''){
                echo "make a class selection correctly";
                http_response_code(406);
            } else {
                $selection= "SELECT c.coursename, c.course_id FROM course c, class_course cc, class s
                    WHERE c.course_id= cc.course_id AND s.class_id=cc.class_id AND s.classname='$classname'";
                $result= $this->connection->query($selection);
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $new_array[]= $row;
                    }
                    print_r( json_encode($new_array));
                }else{
                    echo 'Incorrect selection';
                    http_response_code(406);
                }
            }
        }

        public function studentAddition($username,$password,$firstname,$lastname,$classname,$marks1,$marks2, $marks3,$course1,$course2,$course3){
            
            if($username=='' || $password=='' || $firstname== ''|| $lastname=='' ||$classname==''||$course1=='' ||$course2=='' ||$course3=='' || $marks1=='' || $marks2=='' || $marks3=='') {
                echo 'Please enter compelete details';
                http_response_code(406);
            } else {
                
                $statement="INSERT INTO student(username, password, firstname, lastname, delete_status, status) VALUES ('$username', '$password', '$firstname', '$lastname','0','0')";
                $result = $this->connection->query($statement);
                $studentId = mysqli_insert_id($this->connection);

                $statement2= "INSERT INTO student_class (student_id, class_id, delete_status) VALUES(LAST_INSERT_ID(),(SELECT class_id FROM class WHERE classname = '$classname'), '0')";
                $result_1 = $this->connection->query($statement2);

                $sql1 ="INSERT INTO student_course(student_id, class_id, course_id,marks,delete_status) VALUES($studentId,(SELECT class_id FROM class WHERE classname = '$classname'),$course1,$marks1,'0')";
                $res_sql1 = $this->connection->query($sql1);

                $sql2 ="INSERT INTO student_course(student_id, class_id, course_id,marks,delete_status) VALUES($studentId,(SELECT class_id FROM class WHERE classname = '$classname'),$course2,$marks2,'0')";
                $res_sql2 = $this->connection->query($sql2);

                $sql3 ="INSERT INTO student_course(student_id, class_id, course_id,marks,delete_status) VALUES($studentId,(SELECT class_id FROM class WHERE classname = '$classname'),$course3,$marks3,'0')";
                $res_sql3 = $this->connection->query($sql3);
                
                if(mysqli_affected_rows($this->connection)>0) {
                    echo '-----rows inserted';
                }
                else{
                    echo '-----insertion fault';
                    http_response_code(406);
                }
            }
        }

        public function studentSelection($classname){
            if($classname==''){
                echo "make a class selection correctly";
                http_response_code(406);
            } else {
                $statement4= "SELECT s.student_id, concat(s.firstname,'_',s.lastname) AS student_name FROM student_class sc, student s, class c WHERE sc.class_id = c.class_id AND s.student_id= sc.student_id AND c.classname='$classname'";
                $result_4 = $this->connection->query($statement4);
                if($result_4->num_rows > 0) {
                    while($row = $result_4->fetch_assoc()) {
                        $new_arr[]= $row;
                    }
                    print_r( json_encode($new_arr));
                }else{
                    echo 'Incorrect selection';
                    http_response_code(406);
                }
            }
        }

        public function studentdetails($student){
            if($student==''){
                echo "student selection invalid";
                http_response_code(406);
            } else {
                $statement5= "SELECT c.coursename,sc.marks from student_course as sc, student as s, course as c  where sc.course_id=c.course_id and sc.student_id=s.student_id and sc.student_id='$student';";
                $result_5 = $this->connection->query($statement5);

                if($result_5->num_rows > 0) {
                    while($row = $result_5->fetch_assoc()) {
                        $new_arr[]= $row;
                    }
                    print_r( json_encode($new_arr));
                }else{
                    echo 'Incorrect selection';
                    http_response_code(406);
                }
            }
        }
    }

    $request = json_decode(file_get_contents("php://input"));
    $action = $request->action;
    $user1= new studentService();
    switch($action){
        case 'userAuthenticate':
            $username = $request->username;
            $password = $request->password;
            $user1->userAuthenticate($username,$password);
            break;
        case 'courseSelection':
            $classname = $request->classname;
            $user1->courseSelection($classname);
            break;
        case 'studentAddition':
            $username = $request->username;
            $password = $request->password;
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $classname = $request->classname;
            $marks1 = $request->marks1;
            $marks2 = $request->marks2;
            $marks3 = $request->marks3;
            $course1= $request->course1;
            $course2= $request->course2;
            $course3= $request->course3;
            $user1->studentAddition($username,$password,$firstname,$lastname,$classname,$marks1,$marks2, $marks3,$course1,$course2,$course3);
            break;
        case 'studentSelection':
            $classname=$request->classname;
            $user1->studentSelection($classname);
            break;
        case 'studentdetails':
            $student=$request->student;
            $user1->studentdetails($student);
            break;
        case ' ':
            echo 'empty action variable';
            break; 
    }
    session_unset();
    session_destroy();
?>