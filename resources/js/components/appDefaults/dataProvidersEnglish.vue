<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3 class="mb-0">Data Providers</h3>
                </div>
                <div class="col-auto">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="toggleModule"
                    >{{module.icon}}</button>
                </div>
            </div>
        </div>
        <div class="card-body" v-if="module.display">
            <div class="accordion" id="dataProvidersEnglish">
                <div class="card" v-for="(data_provider,index) in data_providers" :key="index">
                    <div class="col-md-12" v-if="editdata_providers.status && editdata_providers.index==index">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" v-model="data_provider.title" />
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea
                                class="form-control"
                                rows="8"
                                placeholder="CONTENT GOES HERE"
                                v-model="data_provider.content"
                            ></textarea>
                        </div>
                        <div class="text-right">
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                @click="update"
                            >Update</button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="cancelUpdate(index)"
                            >Cancel</button>
                        </div>
                    </div>
                    <template v-else>
                        <div
                            class="card-header"
                            :id="'heading-'+index"
                            data-toggle="collapse"
                            :data-target="'#collapse-'+index"
                            aria-expanded="false"
                            aria-controls="collapseOne"
                        >
                            <h5 class="mb-0">{{index+1}}. {{data_provider.title}}</h5>
                        </div>
                        <div
                            :id="'collapse-'+index"
                            class="collapse"
                            :aria-labelledby="'heading-'+index"
                            data-parent="#dataProvidersEnglish"
                        >
                            <div class="card-body">
                                <p>{{data_provider.content}}</p>
                                <div class="text-right">
                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm"
                                        @click="edit(index)"
                                    >Edit</button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        @click="deletedata_provider(index)"
                                    >Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="row" v-if="adddata_providers">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" v-model="newdata_providers.title" />
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea
                            class="form-control"
                            rows="8"
                            placeholder="CONTENT GOES HERE"
                            v-model="newdata_providers.content"
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="adddata_provider"
                    v-if="!adddata_providers"
                >Add More</button>
                <button
                    type="button"
                    class="btn btn-info btn-sm"
                    @click="savedata_provider"
                    v-if="adddata_providers"
                >Save</button>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="cancelAdd"
                    v-if="adddata_providers"
                >Cancel</button>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            adddata_providers: false,
            editdata_providers: {
                index: null,
                status: false
            },
            editBtn: true,
            newdata_providers: {
                title: "",
                content: ""
            },
            errors: {}
        };
    },
    methods: {
        edit(index) {
            this.editdata_providers = {
                index: index,
                status: true
            };
            this.editBtn = false;
        },
        cancelUpdate(index) {
            this.editdata_providers = {
                index: null,
                status: false
            };
            this.editBtn = true;
            this.$store.dispatch("getAppDefaults");
        },
        update() {
            this.save();
        },
        deletedata_provider(index) {
            this.$swal({
                title: "Are you sure?",
                text: "You may not undo this",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    this.data_providers.splice(index, 1);
                    this.save();
                }
            });
        },
        adddata_provider() {
            this.adddata_providers = true;
        },
        cancelAdd() {
            (this.adddata_providers = false),
                (this.newdata_providers = {
                    title: "",
                    content: ""
                });
        },
        savedata_provider() {
            this.data_providers.push(this.newdata_providers);
            this.newdata_providers = {
                title: "",
                content: ""
            };
            (this.adddata_providers = false), this.save();
        },
        save() {
            var data = {
                saveType: "data_providers",
                data_providers: {
                    en: this.data_providers,
                    ar: this.data_providersArabic
                }
            };
            axios
                .post("/appDefaults", data)
                .then(response => {
                    this.editdata_providers = {
                        index: null,
                        status: false
                    };
                    this.editBtn = true;
                    this.$store.dispatch("getAppDefaults");
                    showNotify("success", "Saved Successfully");
                })
                .catch(error => {
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        toggleModule() {
            this.$parent.toggleModule("data_providers");
        }
    },
    computed: {
        module() {
            return this.$parent.modules.data_providers;
        },
        data_providers() {
            return this.$parent.appDefaults.data_providers.en;
        },
        data_providersArabic() {
            return this.$parent.appDefaults.data_providers.ar;
        }
    }
};
</script>