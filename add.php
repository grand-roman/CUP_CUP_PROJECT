<?php

require_once("functions.php");
require_once("init.php");



if (!empty($_SESSION)) {

$errors_task = [];
$task = [];

$projects = getProjects ($user_id);

$tasks = getTasks($user_id, null, null);

$taskall = getAllTasks($user_id); 

$user = getUser($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $required_fields = ['name'];

  $task = ['name' => $_POST['name'] ?? null,
  'date' => empty($_POST['date']) ? null: date('Y-m-d', strtotime($_POST['date'])),
  'project' => $_POST['project'] ?? null, 
  'file' => null
  ];
  
  foreach ($required_fields as $required_field) {
    if (empty($task[$required_field])) {
      $errors_task[$required_field] = 'Обязательно заполните это поле';
    }
  }

  if(!empty($task['date']) && strtotime($task['date']) < strtotime(date('Y-m-d')))
  {

    $errors_task['date'] = 'Дата должна быть больше текущей';
  }

  if(count($errors_task)===0 && isset($_FILES['preview']['tmp_name'])
    && is_uploaded_file($_FILES['preview']['tmp_name']))
  {
       $file_task = isset($_FILES['preview']['name']) ?
       $_FILES['preview']['name'] : 'default_filename';
       $file_path = $_FILES['preview']['tmp_name'];

       move_uploaded_file($file_path, __DIR__ . '/uploads/'. $file_task);
       $task['file'] = $file_task;
  }

  if (count($errors_task)===0){

      setTasks($user_id,
      $task['name'],
      $task['project'],
      $task['date'],
      $task['file']);
    header('Location: /');
  }
}

$add_content = include_template('add.php', [
  "project_tasks" => $projects,
  "errors_task" => $errors_task,
  "task" => $task
]);

$add_layout_content = include_template('layout.php', [
    "project_tasks" => $projects,
    "tasks_with_information" => $tasks,
    "content" => $add_content,
    "title" => "CUP CUP PROJECT",
    "tasks_all" => $taskall,
    "user_name" => $user[0]['name_user']
]);

}
 else {
  $add_layout_content = include_template('guest.php', [
        'title' => 'CUP CUP PROJECT']);
}

print($add_layout_content);
?>