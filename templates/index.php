
 <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.php" method="get">
                    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                      <a href="/index.php<?php if(isset($project_id)):?>?project_id=<?=$project_id?><?php endif; ?>" class="tasks-switch__item <?php if ($filter === ''): ?>tasks-switch__item--active <?php endif; ?>">Все задачи</a>
                      <a href="/index.php?filter=today<?php if(isset($project_id)):?>&project_id=<?=$project_id?><?php endif; ?>" class="tasks-switch__item <?php if ($filter === 'today'): ?>tasks-switch__item--active <?php endif; ?>">Повестка дня</a>
                      <a href="/index.php?filter=tomorrow<?php if(isset($project_id)):?>&project_id=<?=$project_id?><?php endif; ?>" class="tasks-switch__item <?php if ($filter === 'tomorrow'): ?>tasks-switch__item--active <?php endif; ?>">Завтра</a>
                      <a href="/index.php?filter=overdue<?php if(isset($project_id)):?>&project_id=<?=$project_id?><?php endif; ?>" class="tasks-switch__item <?php if ($filter === 'overdue'): ?>tasks-switch__item--active <?php endif; ?>">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <input class="checkbox__input visually-hidden show_completed" type="checkbox"<?php if ($show_complete_tasks === 1): ?> checked<?php endif; ?>>
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                    <?php foreach($tasks_with_information as $task): ?>
                        <?php if (isset($task["status"]) && $task["status"] === 0 ): ?>
                            <tr 
                                <tr class="tasks__item task<?= Task_Important($task) ? " task--important" : '';?>">
                                <td class="task__select">
                                    <label class="checkbox task__checkbox">
                                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?php if (isset($task['id'])): ?> value="<?=$task['id'];?><?php endif; ?>">
                                        <span class="checkbox__text"><?php if (isset($task["name_task"])): ?> <?= strip_tags($task["name_task"]);  ?> <?php endif; ?></span>
                                    </label>
                                </td>

                                <td class="task__file">
                                    <?php if (isset($task["file_task"])): ?>
                                            <a class="<?= ($task['file_task'] ? "download-link" : ""); ?>" href="uploads/<?= $task['file_task']; ?>" target="_blank"> 
                                            <?= strip_tags($task["file_task"]);  ?> <?php endif; ?></a>
                                </td>

                                <td class="task__date"><?php if (isset($task["deadline"])): ?> <?= strip_tags($task["deadline"]);  ?> <?php endif; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($show_complete_tasks === 1 && isset($task["status"]) && $task["status"] === 1): ?>
                                <tr class="tasks__item task task--completed">
                                    <td class="task__select">
                                        <label class="checkbox task__checkbox">
                                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?php if (isset($task['id'])): ?> value="<?=$task['id'];?><?php endif; ?>" checked>
                                            <span class="checkbox__text"><?php if (isset($task["name_task"])): ?> <?= strip_tags($task["name_task"]);  ?> <?php endif; ?></span>
                                        </label>
                                    </td>
                                    <td class="task__date"><?php if (isset($task["deadline"])): ?> <?= strip_tags($task["deadline"]);  ?> <?php endif; ?></td>

                                    <td class="task__controls">
                                    </td>
                                </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>