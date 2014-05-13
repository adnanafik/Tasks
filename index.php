<?php
//composer autoload
require_once './vendor/autoload.php';

ActiveRecord\Config::initialize(function($cfg)
{
    $cfg->set_model_directory('models');
    $cfg->set_connections(array(
    'development' => 'pgsql://sid:foo.bar@localhost/my_db'
    ));

});

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->hook('slim.before.dispatch', function () use ($app) {

    echo '
    <html>
        <head>
        <title>List of Tasks</title>

        <!-- add styles -->

        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js" ></script>';

        echo '<script>';
        echo '$(function(){';
        echo  '$.datepicker.setDefaults(';
        echo  '$.extend($.datepicker.regional[\'\'])';
        echo ');';
        echo '$(\'#datepicker\').datepicker();';
        echo '});';
        echo '</script>
        </head>
    <body>';


});

$app->hook('slim.after.dispatch', function () use ($app) {
    echo '</body></html>';
});

$app->get('/', function () {

    //echo 'hello world';
    echo '<!-- Button trigger modal -->
            <br><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
            New Task
            </button><br><br>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-body">
                            <form action="/tasks" method="post">
                                <label>Title</label><br>
                                <input type="text" name="TaskTitle"><br>

                                <label>Description</label><br>
                                <textarea class="form-control" rows="3" name="TaskDesc"></textarea><br>

                                <label>Due Date</label><br>
                                <input id="datepicker" type="text" name="DueDate"/><br><br>

                                <input type="submit" class="btn btn-primary"  value="Create">
                            </form>
                        </div>

                    </div>
                </div>
            </div>';

    //get all the entries and display the results
    $tasks = Tasks::find('all');

    echo '<table class="table table-striped table-bordered table-condensed">';
    echo '<tr>';
    echo '<th>Title</th>';
    echo '<th>Description</th>';
    echo '<th>DueDate</th>';
    echo '</tr>';
    foreach ($tasks as $t)
    {
        echo'<tr>';
        echo '<td>'.$t->title.'</td>';
        echo '<td>'.$t->desc.'</td>';
        echo '<td>'.$t->due_date.'</td>';
        echo'</tr>';
    }

    echo '</table>';

    //create form for create and list of tasks

});

$app->post('/tasks', function () {

    //echo 'Hello tasks';

    $title    = $_POST['TaskTitle'];
    $desc     = $_POST['TaskDesc'];
    $date     = $_POST['DueDate'];

    /*Tasks::create(array(

        'title' => $title
    ,'desc' => $desc
    ,'due_date' => $date

    ));*/

    $tasks = new Tasks();
    $tasks->title = $title;
    $tasks->desc= $desc;
    $tasks->due_date = $date;
    $tasks->save();

    $tasks = Tasks::last();
    $task_id = $tasks->task_id;
?>

    <a href="/index.php">Back to List</a><br>

    <!-- Button trigger modal -->
            <br><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
            Edit
            </button>

            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal1">
                Delete
            </button><br><br>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-body">
                            <form action="/tasks/<?php echo $task_id; ?>" method="post">
                                <label>Title</label><br>
                                <input type="hidden" name="TaskID" value="<?php echo $task_id ?>"/>
                                <input type="text" name="TaskTitle" value="<?php echo $title ?>"/><br>

                                <label>Description</label><br>
                                <textarea class="form-control" rows="3" name="TaskDesc"><?php echo $desc ?></textarea><br>

                                <label>Due Date</label><br>
                                <input id="datepicker" type="text" name="DueDate" value="<?php echo $date ?>"/><br><br>

                                <input type="submit" class="btn btn-primary"  value="Update">
                            </form>
                        </div>

                    </div>
                </div>
            </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <form action="/tasks_del/<?php echo $task_id; ?>" method="post">
                        <label>Title</label><br>
                        <input type="hidden" name="TaskID" value="<?php echo $task_id ?>"/>
                        <input type="text" name="TaskTitle" value="<?php echo $title ?>"/><br>

                        <label>Description</label><br>
                        <textarea class="form-control" rows="3" name="TaskDesc"><?php echo $desc ?></textarea><br>

                        <label>Due Date</label><br>
                        <input id="datepicker" type="text" name="DueDate" value="<?php echo $date ?>"/><br><br>

                        <input type="submit" class="btn btn-primary"  value="Delete">
                    </form>
                </div>

            </div>
        </div>
    </div>

<?php

    echo '<h3>Title</h3>';

    echo $title;'<br>';

    echo '<h3>Description</h3>';
    echo $desc;'<br>';

    echo '<h3>Due Date</h3>';
    echo $date;

})->name('/tasks');

$app->post('/test', function () {
    echo 'test';
});

$app->post('/tasks/:id', function ($id) use ($app){

    //echo 'postttttt   ';

    $task_id  = $_POST['TaskID'];
    $title    = $_POST['TaskTitle'];
    $desc     = $_POST['TaskDesc'];
    $date     = $_POST['DueDate'];

    //echo $task_id;
    //echo $title;
    //echo $desc;
    //echo $date;

    $tasks = Tasks::find_by_task_id($task_id);

    $tasks->title = $title;
    $tasks->desc= $desc;
    $tasks->due_date = $date;
    $tasks->save();

    //$app = \Slim\Slim::getInstance();
    //$app->redirect('/tasks');
    //$url = $app->urlFor('/tasks');
    //$url->redirect();
    $app->redirect('/');


});

$app->post('/tasks_del/:id', function ($id) {

    //echo 'postttttt   ';

    $task_id  = $_POST['TaskID'];

    //echo $task_id;
    //echo $title;
    //echo $desc;
    //echo $date;

    $tasks = Tasks::find_by_task_id($task_id);

    //delete record identified by task_id
    $tasks->delete();

    $app = \Slim\Slim::getInstance();

    //redirect to main page
    $app->redirect('/');


});

$app->run();

?>