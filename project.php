<?php
require_once ('init.php');
require_once("functions.php");

if (!empty($_SESSION)) {


$errors_project = [];
$project = [];

$projects = getProjects ($user_id);
$tasks = getTasks($user_id, null, null);
$taskall = getAllTasks($user_id); 
$user = getUser($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $required_fields = ['name'];

  $project = ['name' => $_POST['name'] ?? null ];

	if (empty($project['name'])) {
    $errors_project['name'] = 'Это поле надо заполнить';
	}
  else {

    $res = checkProject($user_id, $project['name']);

  if (mysqli_num_rows($res) > 0) {
    $errors_project['name'] = 'Такой проект уже существует';
  }

  }
  
  if (count($errors_project) === 0) {

    setProject($user_id,
      $project['name']);
    header('Location: /');
  }
}
$project_content = include_template('project.php', [
  "project_tasks" => $projects,
  "errors_project" => $errors_project,
]);

$project_layout_content = include_template('layout.php', [
    "project_tasks" => $projects,
    "tasks_with_information" => $tasks,
    "content" => $project_content,
    "title" => "CUP CUP PROJECT",
    "tasks_all" => $taskall,
    "user_name" => $user[0]['name_user']
]);

}
 else {
  $project_layout_content = include_template('guest.php', [
        'title' => 'CUP CUP PROJECT']);
}

print($project_layout_content);
?>