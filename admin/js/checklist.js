/**
 * Created by User on 04.01.2015.
 */


function CheckList() {

    var activeListContainer = document.getElementById('activeList');

    function addItem(taskID, taskNameStr){
        var taskItem = document.createElement("div");
        taskItem.classList.add("task");
        taskItem.id = taskID;

        var taskName = document.createElement("span");
        taskName.classList.add("taskName");
        taskName.innerHTML = taskNameStr;
        taskItem.appendChild(taskName);

        var responsible = createAdminSelect();
        taskItem.appendChild(responsible);

        var checkCompleted = document.createElement("input");
        checkCompleted.classList.add("checkCompleted");
        checkCompleted.setAttribute("type", "checkbox");
        taskItem.appendChild(checkCompleted);

        activeListContainer.appendChild(taskItem);

    }

    function createAdminSelect() {
        var responsible = document.createElement("select");
        responsible.classList.add('responsible');

        var responsibleOption = document.createElement("option");
        responsibleOption.innerHTML = "&lt;Select&gt;";
        responsible.appendChild(responsibleOption);

        responsibleOption = document.createElement("option");
        responsibleOption.innerHTML = "Лёша";
        responsible.appendChild(responsibleOption);

        responsibleOption = document.createElement("option");
        responsibleOption.innerHTML = "Соня";
        responsible.appendChild(responsibleOption);

        return responsible;
    }


    var addButton = document.getElementById('addButton');
    addButton.addEventListener('click', function () {
        var taskName = document.getElementById('addText').value;

        $.ajax({
            url: 'checklist_ajax.php',
            data: {
                'action': 'addItem',
                'taskName': taskName
            },
            dataType: 'json',
            success: function (data) {

                if (data.successful) {
                    //alert('Success!');
                    //$currentLink.parent().parent().text(basket);
                    addItem(data.itemID, taskName);
                    console.log("Task " + data.taskID + " added");
                }
                else {
                    //alert('Error');
                    //$currentLink.parent().parent().text(basket);
                    console.log("Failed to add task " + data.taskID + ": " + data.msg);
                }

            },
            error: function (data) {
                //alert('Error!');
                console.log("Failed to add task " + data.taskID + ": " + data.msg);
            }
        });

        return false;

    });

    $.ajax({
        url: 'checklist_ajax.php',
        data: {
            'action': 'getItems'
        },
        dataType: 'json',
        success: function (data) {

            if (data.successful) {
                var itemsData = data.itemsData;
                //console.log("itemsData:  " + itemsData);
                //console.log("itemsData size:  " + itemsData.length);
                itemsData.forEach(function(item){
                    addItem(item.id, item.task_name);
                })
            }
            else {
                console.log("Failed to get items: " + data.msg);
            }

        },
        error: function (data) {
            console.log("Failed to get items: " + data.msg);
        }
    });


}