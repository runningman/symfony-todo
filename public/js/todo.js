var app = new Vue({
    el: '#todoList',
    data: {
        tasks: [],
        description: '',
        tasksUrl: '',
        addUrl: '',
    },

    methods: {
        addTask: function () {
            let payload = {description: this.description};

            axios.post(this.addUrl, payload).then((response) => {
                console.log(response.data);
                this.tasks.push(response.data);
                this.description = '';
            }).catch(() => {
                alert('Failed to add the task');
            });
        },

        updateTask: function (task, event) {
            task.is_complete = event.target.checked;

            let updateUrl = this.addUrl + '/' + task.uuid;
            let payload = {is_complete: task.is_complete};

            axios.post(updateUrl, payload).catch(() => {
                alert('Failed to update task');
            });
        }
    },

    // Load all the tasks for the list.
    mounted() {
        this.tasksUrl = document.getElementById('todoList').getAttribute('tasks-url');
        this.addUrl = document.getElementById('todoList').getAttribute('add-url');

        axios.get(this.tasksUrl).then((response) => {
            this.tasks = response.data;
        }).catch(() => {
            alert('Failed to load tasks');
        });
    },

    computed: {
        completedTasks: function () {
            return this.tasks.filter((task) => { return task.is_complete; }).length;
        },
        todoTasks: function () {
            return this.tasks.filter((task) => { return !task.is_complete; }).length;
        }
    }
});