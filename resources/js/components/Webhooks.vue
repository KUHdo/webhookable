<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        WebHooks
                    </span>

                    <a class="action-link" tabindex="-1" @click="showCreateWebHookForm">
                        Create New WebHook
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Current webHooks -->
                <p class="mb-0" v-if="webHooks.length === 0">
                    You have not created any WebHooks.
                </p>

                <table class="table table-borderless mb-0" v-if="webHooks.length > 0">
                    <thead>
                        <tr>
                            <th>WebHook ID</th>
                            <th>URL</th>
                            <th>Event</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="webHook in webHooks">
                            <!-- ID -->
                            <td style="vertical-align: middle;">
                                {{ webHook.id }}
                            </td>

                            <!-- Name -->
                            <td style="vertical-align: middle;">
                                {{ webHook.url }}
                            </td>

                            <!-- Secret -->
                            <td style="vertical-align: middle;">
                                <code>{{ webHook.event }}</code>
                            </td>

                            <!-- Edit Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link" tabindex="-1" @click="edit(webHook)">
                                    Edit
                                </a>
                            </td>

                            <!-- Delete Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link text-danger" @click="destroy(webHook)">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create webHook Modal -->
        <div class="modal fade" id="modal-create-webHook" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Create WebHook
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in createForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create webHook Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">WebHook URL</label>

                                <div class="col-md-9">
                                    <input id="create-webHook-url" type="text" class="form-control"
                                                                @keyup.enter="store" v-model="createForm.url">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">WebHook Event</label>

                                <div class="col-md-9">
                                    <select class="form-control" name="event"
                                            @keyup.enter="store"
                                            v-model="createForm.event">
                                        <option v-for="option in events">
                                            {{ option }}
                                        </option>
                                    </select>
                                    <span class="form-text text-muted">
                                        Your application's callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="store">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit webHook Modal -->
        <div class="modal fade" id="modal-edit-webHook" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Edit webHook
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit webHook Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">WebHook URL</label>

                                <div class="col-md-9">
                                    <input id="edit-webHook-name" type="text" class="form-control"
                                                                @keyup.enter="update" v-model="editForm.url">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- web hook event -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Event</label>

                                <div class="col-md-9">
                                    <select class="form-control" name="event"
                                            @keyup.enter="update"
                                            v-model="editForm.event">
                                        <option v-for="option in events">
                                            {{ option }}
                                        </option>
                                    </select>

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="update">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                webHooks: [],
                events: [],

                createForm: {
                    errors: [],
                    url: '',
                    event: ''
                },

                editForm: {
                    errors: [],
                    url: '',
                    event: ''
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getWebHooks();
                this.getPossibleEvents();

                $('#modal-create-webHook').on('shown.bs.modal', () => {
                    $('#create-webHook-url').focus();
                });

                $('#modal-edit-webHook').on('shown.bs.modal', () => {
                    $('#edit-webHook-url').focus();
                });
            },

            /**
             * Get all of the OAuth webHooks for the user.
             */
            getWebHooks() {
                axios.get('/api/subscription')
                        .then(response => {
                            this.webHooks = response.data;
                        });
            },
            /**
             * Get all of the possible events for a web hook.
             */
            getPossibleEvents() {
                axios.get('/api/polling/trigger')
                    .then(response => {
                        this.events = response.data;
                    });

            },
            /**
             * Show the form for creating new webHooks.
             */
            showCreateWebHookForm() {
                $('#modal-create-webHook').modal('show');
            },

            /**
             * Create a new OAuth webHook for the user.
             */
            store() {
                this.persistWebHook(
                    'post', '/api/subscription',
                    this.createForm, '#modal-create-webHook'
                );
            },

            /**
             * Edit the given webHook.
             */
            edit(webHook) {
                this.editForm.id = webHook.id;
                this.editForm.url = webHook.url;
                this.editForm.event = webHook.event;

                $('#modal-edit-webHook').modal('show');
            },

            /**
             * Update the webHook being edited.
             */
            update() {
                this.persistWebHook(
                    'put', '/api/subscription/' + this.editForm.id,
                    this.editForm, '#modal-edit-webHook'
                );
            },

            /**
             * Persist the webHook to storage using the given form.
             */
            persistWebHook(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getWebHooks();

                        form.url = '';
                        form.event = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given webHook.
             */
            destroy(webHook) {
                axios.delete('/api/subscription/' + webHook.id)
                        .then(response => {
                            this.getWebHooks();
                        });
            }
        }
    }
</script>
