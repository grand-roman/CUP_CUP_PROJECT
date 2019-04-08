<?php

require_once("functions.php");
require_once("init.php");

if (!empty($_SESSION)) {

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;

$projects = getProjects ($user_id);

$filter =  isset($_GET['filter']) ? $_GET['filter'] : '';

$tasks = getTasks($user_id, $project_id, $filter);

$taskall = getAllTasks($user_id); 

$user = getUser($user_id);



$show_complete_tasks = isset($_GET['show_completed']) ? intval($_GET['show_completed']) : 0;


if(isset($_GET['task_id']) && isset($_GET['check'])) {
    $task_id = intval($_GET['task_id']);
    $result = getCompleted($task_id, $_GET['check']);
    header("Location: /index.php");
}

if(isset($_GET['search'])) {

    $tasks = searchTask($_GET['search']);
}


if($project_id === 0 || count($tasks) === 0) {
	$page_content = "<p>Ничего не найдено по вашему запросу</p>";
	http_response_code(404);
}
else {

$page_content = include_template("index.php", [
    "tasks_with_information" => $tasks,
    "show_complete_tasks" => $show_complete_tasks,
    "filter" => $filter,
    "project_id" => $project_id
]);

}

$layout_content = include_template("layout.php", [
    "project_tasks" => $projects,
    "tasks_with_information" => $tasks,
    "content" => $page_content,
    "title" => "CUP CUP PROJECT",
    "tasks_all" => $taskall,
    'user_name' => $user[0]['name_user']
]);
}
 else {
	$layout_content = include_template('guest.php', [
        'title' => 'CUP CUP PROJECT']);
}
print($layout_content);

?>