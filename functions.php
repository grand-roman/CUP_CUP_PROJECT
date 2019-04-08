<?php
require_once("mysql_helper.php");
error_reporting(E_ALL);

/**
 * @param $name
 * @param $data
 * @return false|string
 */
function include_template($name, $data) {
    $name = "templates/" . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
};



/**
 * Подсчитывает число задач для заданного проекта
 *
 * @param array $task_with_information
 * @param string $project_task
 *
 * @return int
 */
function Counting_Task ($task_with_information, $project_task ) {

    $count = 0;
    foreach ($task_with_information as $task) {
        if (isset($task["project_id"]) && $project_task === $task["project_id"]) {
            $count++;
        }
    }
    return $count;
}

/**
 * Возвращает true если до даты выполнения осталось меньше 24 часов или задача остлось не решенной 
 *
 * @param array $task
 *
 * @return boolean
 */
function Task_Important ($task){

    if (isset($task["status"]) && (((strtotime($task["deadline"])-time())<=86400) || (time()>=strtotime($task["deadline"]))))
    {
        return true;
    }
}

/**
 * Создает массив на основе готового SQL запроса и переданных данных
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return array результ SQL запроса | null
 */
function request ($link, $sql, $data = []) {
 $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    if ($result = mysqli_stmt_get_result($stmt)) {
         $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $res;
        
    }
    else {
        http_response_code(503);
        $error = mysqli_error($link);
        print("Ошибка MySQL:" .$error);
        exit();
    }
}

/**
 * Class DbConnectionProvider
 */
class DbConnectionProvider
{
    protected static $connection;

    public static function getConnection()
    {
         if (self::$connection === null) {
              self::$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if(!self::$connection) {

            http_response_code(503);
            print("Ошибка MySQL: connection failed");
            exit();
            }

            mysqli_set_charset(self::$connection, "utf8");
        }

         return self::$connection;
    }
}

/**
 * SQL-запрос для получения списка проектов у текущего пользователя
 *
 * @param int $user_id - id пользователя
 *
 * @return array результ SQL запроса
 */
function getProjects ($user_id){

    $connection = DbConnectionProvider::getConnection();
    $sql = "SELECT id, name_project FROM Project WHERE user_id = ?";

    $parameters = [$user_id];

    return request($connection, $sql, $parameters);
}
/**
 * SQL-запрос для получения списка из всех задач на определенный проект у текущего пользователя
 *
 * @param int $user_id - id пользователя
 * @param int | null $project_id - id проекта
 * @param string | null $time - выбор данного филтра
 *
 * @return array результ SQL запроса
 */
function getTasks($user_id, $project_id = null, $time = null){

    $connection = DbConnectionProvider::getConnection();

    $sql =  "SELECT id, name_task, file_task, deadline, status, user_id, project_id
            FROM Task WHERE user_id = ?";
    $parameters = [$user_id];

    if($project_id != null){
        $sql .= " AND project_id = ?";
        $parameters[]= $project_id;
    }

    if($time != null){
        switch ($time) {
            case 'today': {
                $sql .=  " AND TO_DAYS(NOW()) - TO_DAYS(deadline) = 0";
                break;
            }
            case 'tomorrow': {
                $sql .=  " AND TO_DAYS(NOW()) + 1 - TO_DAYS(deadline) = 0";
                break;
            }
            case 'overdue': {
                $sql .=   " AND status = 0 AND TO_DAYS(NOW()) - TO_DAYS(deadline) > 0";
                break;
            }
            default:
                break;
        }
    }
    
    return request($connection, $sql, $parameters);
}
/**
 * SQL-запрос для получения списка из всех задач у текущего пользователя
 *
 * @param int $user_id - id пользователя
 *
 * @return array результ SQL запроса
 */
function getAllTasks($user_id){

    $connection = DbConnectionProvider::getConnection();
     $sql =  "SELECT id, name_task, file_task, deadline, status, user_id, project_id
            FROM Task WHERE user_id = ?";
    $parameters = [$user_id];

    return request($connection, $sql, $parameters);
}
/**
 * SQL-запрос для добавление задачи у текущего пользователя
 *
 * @param int $user_id - id пользователя
 * @param string $name_task - id пользователя
 * @param int $project_id - id проекта
 * @param string|null $date - дата окончания проекта
 * @param string|null $file - файл задачи
 *
 */
function setTasks(int $user_id, string $name_task, int $project_id, ?string $date, ?string $file){

    $connection = DbConnectionProvider::getConnection();
    $sql = 'INSERT INTO Task SET user_id = ?, name_task = ?, creation_at = NOW()';
    $values = [$user_id, $name_task];

    if ($project_id !== 0) {
    $sql .= ', project_id = ?';
    $values[] = $project_id;
    }   else {
        $sql .= ', project_id = NULL';
    }



    if ($date !== null) {
    $sql .= ', deadline = ?';
    $values[] = $date;
    }

    if ($file !== null) {
    $sql .= ', file_task = ?';
    $values[] = $file;
    }

    $stmt = db_get_prepare_stmt($connection, $sql, $values);
    mysqli_stmt_execute($stmt);
}
/**
 * SQL-запрос для получения проекта у пользователя
 *
 * @param int $user_id - id пользователя
 * @param string $project - имя проекта
 *
 * @return bool|mysqli_result
 */
function checkProject(int $user_id, string $project){

    $connection = DbConnectionProvider::getConnection();
    $resuser = mysqli_real_escape_string($connection, $user_id);
    $resproject = mysqli_real_escape_string($connection, $project);
    $sql = "SELECT name_project FROM Project WHERE name_project = '" . $resproject ."'AND user_id = ' " . $resuser . "' LIMIT 1";
    $res = mysqli_query($connection, $sql);

    return $res;
}
/**
 * SQL-запрос для добавление проекта у текущего пользователя
 *
 * @param int $user_id - id пользователя
 * @param string $project - имя проекта
 *
 */
function setProject(int $user_id, string $project){

    $connection = DbConnectionProvider::getConnection();
    $sql = 'INSERT INTO Project SET user_id = ?, name_project = ?';
    $values = [$user_id, $project];

    $stmt = db_get_prepare_stmt($connection, $sql, $values);
    mysqli_stmt_execute($stmt);
}

/**
 * SQL-запрос для регестрации пользователя
 *
 * @param string $email - почта пользователя
 * @param string $name - имя пользователя
 * @param string $password - пароль пользователя
 */
function regUser($email,$name,$password){

    $connection = DbConnectionProvider::getConnection();
    $sql = 'INSERT INTO User SET email=?, name_user=?, password=?, reg_date=NOW()';
    $values = [$email,$name,$password];

    $stmt = db_get_prepare_stmt($connection, $sql, $values);
    mysqli_stmt_execute($stmt);
}
/**
 * SQL-запрос для получения почты пользователя, чтоыб првоерить сущетсвует ли такой пользователь 
 *
 * @param string $repeat_email - почта пользователя
 *
 * @return array результ SQL запроса
 */
function repeatEmail($repeat_email){

    $connection = DbConnectionProvider::getConnection();
    $sql = "SELECT * FROM User WHERE email = ?";
    $parameters = [$repeat_email];

    return  request($connection, $sql, $parameters);
}
/**
 * SQL-запрос для получения почты пользователя, чтоыб првоерить правильно ли вел пользователь данные 
 *
 * @param string $repeat_email - почта пользователя
 *
 * @return array результ SQL запроса | null
 */
function logUser($email){

    $connection = DbConnectionProvider::getConnection();
    $resemail = mysqli_real_escape_string($connection, $email);
    $sql = "SELECT * FROM User WHERE email = '". $resemail ."'";
    $res = mysqli_query($connection, $sql);
    $parameters = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : NULL;

    return $parameters;
}
/**
 * SQL-запрос для получения данных пользователя
 *
 * @param int $user_id - id пользователя
 *
 * @return array результ SQL запроса
 */
function getUser($user_id){

    $connection = DbConnectionProvider::getConnection();
    $sql =  "SELECT *
            FROM User WHERE id = ?";
    $parameters = [$user_id];

    return request($connection, $sql, $parameters);

}
/**
 * SQL-запрос для пометки задачи как выполненную
 *
 * @param int $task_id - id пользователя
 *
 * @param string $check - GET запрос на выполненую задачу
 */
function getCompleted($task_id, $check){

    $connection = DbConnectionProvider::getConnection();
    if($check === '1') {
        $sql = "UPDATE Task SET status = 1, done_at = NOW() WHERE id = ?";
        $parameters = [$task_id];

    } else {
        $sql = "UPDATE Task SET status = 0, done_at = NULL WHERE id = ?";
        $parameters = [$task_id];
    }

    
    $stmt = db_get_prepare_stmt($connection, $sql, $parameters);
    mysqli_stmt_execute($stmt);

}


/**
 * SQL-запрос для поиска задачи
 *
 * @param $search - задача, которую ищут
 *
 * @return array результ SQL запроса
 *
 */
function searchTask ($search){

    $connection = DbConnectionProvider::getConnection();
    $resSearch = mysqli_real_escape_string($connection, $search);
    $sql = "SELECT * FROM Task WHERE MATCH(name_task) AGAINST( ? )";
    $parameters = [$resSearch];

        return  request($connection, $sql, $parameters);
}
?>
