<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3 class="mb-0">Local Information</h3>
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
            <div class="accordion" id="local_informations_english">
                <div class="card" v-for="(local_information,index) in local_informations" :key="index">
                    <div class="col-md-12" v-if="editlocal_informations.status && editlocal_informations.index==index">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" v-model="local_information.title" />
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea
                                class="form-control"
                                rows="8"
                                placeholder="CONTENT GOES HERE"
                                v-model="local_information.content"
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
                            <h5 class="mb-0">{{index+1}}. {{local_information.title}}</h5>
                        </div>
                        <div
                            :id="'collapse-'+index"
                            class="collapse"
                            :aria-labelledby="'heading-'+index"
                            data-parent="#local_informations_english"
                        >
                            <div class="card-body">
                                <p>{{local_information.content}}</p>
                                <div class="text-right">
                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm"
                                        @click="edit(index)"
                                    >Edit</button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        @click="deletelocal_information(index)"
                                    >Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="row" v-if="addlocal_informations">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" v-model="newlocal_informations.title" />
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea
                            class="form-control"
                            rows="8"
                            placeholder="CONTENT GOES HERE"
                            v-model="newlocal_informations.content"
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="addlocal_information"
                    v-if="!addlocal_informations"
                >Add More</button>
                <button
                    type="button"
                    class="btn btn-info btn-sm"
                    @click="savelocal_information"
                    v-if="addlocal_informations"
                >Save</button>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="cancelAdd"
                    v-if="addlocal_informations"
                >Cancel</button>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            addlocal_informations: false,
            editlocal_informations: {
                index: null,
                status: false
            },
            editBtn: true,
            newlocal_informations: {
                title: "",
                content: ""
            },
            errors: {}
        };
    },
    methods: {
        edit(index) {
            this.editlocal_informations = {
                index: index,
                status: true
            };
            this.editBtn = false;
        },
        cancelUpdate(index) {
            this.editlocal_informations = {
                index: null,
                status: false
            };
            this.editBtn = true;
            this.$store.dispatch("getAppDefaults");
        },
        update() {
            this.save();
        },
        deletelocal_information(index) {
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
                    this.local_informations.splice(index, 1);
                    this.save();
                }
            });
        },
        addlocal_information() {
            this.addlocal_informations = true;
        },
        cancelAdd() {
            (this.addlocal_informations = false),
                (this.newlocal_informations = {
                    title: "",
                    content: ""
                });
        },
        savelocal_information() {
            this.local_informations.push(this.newlocal_informations);
            this.newlocal_informations = {
                title: "",
                content: ""
            };
            (this.addlocal_informations = false), this.save();
        },
        save() {
            var data = {
                saveType: "local_informations",
                local_informations: {
                    en: this.local_informations,
                    ar: this.local_informationsArabic
                }
            };
            axios
                .post("/appDefaults", data)
                .then(response => {
                    this.editlocal_informations = {
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
            this.$parent.toggleModule("local_informations");
        }
    },
    computed: {
        module() {
            return this.$parent.modules.local_informations;
        },
        local_informations() {
            return this.$parent.appDefaults.local_informations.en;
        },
        local_informationsArabic() {
            return this.$parent.appDefaults.local_informations.ar;
        }
    }
};
</script>