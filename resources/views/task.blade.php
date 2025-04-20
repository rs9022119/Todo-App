<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>To-Do List with Confirm Delete Popup</title>

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    input, button {
      padding: 10px;
      margin: 5px 0;
    }
    ul {
      list-style-type: none;
      padding-left: 0;
    }
    li {
      margin: 10px 0;
      display: flex;
      align-items: center;
    }
    .task-text {
      flex: 1;
      margin-left: 10px;
    }

    /* Modal Styles */
    .modal {
      display: none; /* Hidden by default */
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      width: 300px;
    }

    .modal-buttons {
      margin-top: 20px;
    }

    .modal-buttons button {
      padding: 8px 15px;
      margin: 0 10px;
    }
  </style>
</head>
<body>

  <h2>To-Do List</h2>

  <form action="#" id="task_form">
    <input type="text" name="task" id="task" placeholder="Enter a task" required />
    <input type="submit" id="add_task" value="Add Task">
  </form>
  <div id="error_message" style="color: red; margin-top: 10px;"></div>

  <input type="button" id="show_task" onclick="showAllTasks()" value="Show All Tasks">

  <ul id="taskList">
    @foreach ($data as $key => $value)
      <li id="task{{ $value->id }}">
        <input type="checkbox" name="completed[]" class="complete_task" id="completed_{{ $value->id }}" value="{{ $value->id }}" />
        <span class="task-text">{{ $value->task_name }}</span>
        <button onclick="openModal({{ $value->id }})">Delete</button>
      </li>
    @endforeach
  </ul>

  <!--Delete Pop-up Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <p>Are you sure to delete this task?</p>
      <div class="modal-buttons">
        <button onclick="confirmDelete()">Yes</button>
        <button onclick="closeModal()">No</button>
      </div>
    </div>
  </div>

  {{-- Delete-Pop-up show and hide script --}}
  <script>
    let currentDeleteTaskId = null;

    function openModal(taskId) {
      currentDeleteTaskId = taskId;
      document.getElementById("deleteModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("deleteModal").style.display = "none";
      currentDeleteTaskId = null;
    }

    function confirmDelete() {
        if (!currentDeleteTaskId) return;

        $.ajax({
        url: '/delete_task',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            task_id: currentDeleteTaskId
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#task' + currentDeleteTaskId).remove();
                closeModal();
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
        });
    }
  </script>

  {{-- Task-add ajax --}}
  <script>
    $(document).ready(function () {
      $('#task_form').submit(function (e) {
        e.preventDefault();
  
        var task = $('#task').val();
  
        $.ajax({
          url: '/task',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            task: task
          },
          success: function (response) {
            if (response.status === 'error') {
                // Show error message
                $('#error_message').text(response.message);
            } else {
                // Clear previous error message
                $('#error_message').text('');

                // Clear input
                $('#task').val('');

                // Append new task
                $('#taskList').append(renderTask(response.data));
            }
          },
          error: function (xhr, status, error) {
            console.error('Error:', error);
          }
        });
      });
  
      // Render single task li
      function renderTask(task) {
        return `
            <li id="task${task.id}">
            <input type="checkbox" name="completed[]" class="complete_task" id="completed_${task.id}" value="${task.id}" />
            <span class="task-text">${task.task_name}</span>
            <button onclick="openModal(${task.id})">Delete</button>
            </li>
        `;
      }
    });
  </script>

  {{-- Task omplete ajax --}}
  <script>
    $(document).ready(function () {
      $(document).on('click', '.complete_task', function () {

        let taskId = $(this).val();
        let isChecked = $(this).is(':checked') ? 1 : 0;
  
        $.ajax({
          url: '/complete_task',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            task_id: taskId,
            complete_status: isChecked
          },
          success: function (response) {
            if (response.status === 'success') {
                if (isChecked === 1) {
                $('#task' + taskId).remove();
                }
            } else {
                console.error('Error:', response.message);
            }
          },
          error: function (xhr, status, error) {
            console.error('Error:', error);
          }
        });
      });
    });
  </script>

  {{--All-Task show ajax --}}
  <script>
    function showAllTasks() {
        $.ajax({
            url: '/show_all_tasks',
            type: 'GET',
            success: function (response) {
            if (response.status === 'success') {
                let tasks = response.data;
                $('#taskList').empty(); // Clear existing tasks

                tasks.forEach(task => {
                $('#taskList').append(renderTask(task));
                });
            }
            },
            error: function (xhr, status, error) {
            console.error('Error fetching tasks:', error);
            }
        });
    }

    // Render function with checkbox checked condition
    function renderTask(task) {
        let isChecked = task.complete_status == 1 ? 'checked' : '';
        return `
            <li id="task${task.id}">
            <input type="checkbox" name="completed[]" class="complete_task" id="completed_${task.id}" value="${task.id}" ${isChecked} />
            <span class="task-text">${task.task_name}</span>
            <button onclick="openModal(${task.id})">Delete</button>
            </li>
        `;
    }

  </script>

</body>
</html>
