<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h3 class="mb-0">Copyright</h3>
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
            <div class="accordion" id="copyrightEnglish">
                <div class="card" v-for="(copyright,index) in copyrights" :key="index">
                    <div class="col-md-12" v-if="editcopyrights.status && editcopyrights.index==index">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" v-model="copyright.title" />
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea
                                class="form-control"
                                rows="8"
                                placeholder="CONTENT GOES HERE"
                                v-model="copyright.content"
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
                            <h5 class="mb-0">{{index+1}}. {{copyright.title}}</h5>
                        </div>
                        <div
                            :id="'collapse-'+index"
                            class="collapse"
                            :aria-labelledby="'heading-'+index"
                            data-parent="#copyrightEnglish"
                        >
                            <div class="card-body">
                                <p>{{copyright.content}}</p>
                                <div class="text-right">
                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm"
                                        @click="edit(index)"
                                    >Edit</button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        @click="deletecopyright(index)"
                                    >Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="row" v-if="addcopyrights">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" v-model="newcopyrights.title" />
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea
                            class="form-control"
                            rows="8"
                            placeholder="CONTENT GOES HERE"
                            v-model="newcopyrights.content"
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="addcopyright"
                    v-if="!addcopyrights"
                >Add More</button>
                <button
                    type="button"
                    class="btn btn-info btn-sm"
                    @click="savecopyright"
                    v-if="addcopyrights"
                >Save</button>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="cancelAdd"
                    v-if="addcopyrights"
                >Cancel</button>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            addcopyrights: false,
            editcopyrights: {
                index: null,
                status: false
            },
            editBtn: true,
            newcopyrights: {
                title: "",
                content: ""
            },
            errors: {}
        };
    },
    methods: {
        edit(index) {
            this.editcopyrights = {
                index: index,
                status: true
            };
            this.editBtn = false;
        },
        cancelUpdate(index) {
            this.editcopyrights = {
                index: null,
                status: false
            };
            this.editBtn = true;
            this.$store.dispatch("getAppDefaults");
        },
        update() {
            this.save();
        },
        deletecopyright(index) {
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
                    this.copyrights.splice(index, 1);
                    this.save();
                }
            });
        },
        addcopyright() {
            this.addcopyrights = true;
        },
        cancelAdd() {
            (this.addcopyrights = false),
                (this.newcopyrights = {
                    title: "",
                    content: ""
                });
        },
        savecopyright() {
            this.copyrights.push(this.newcopyrights);
            this.newcopyrights = {
                title: "",
                content: ""
            };
            (this.addcopyrights = false), this.save();
        },
        save() {
            var data = {
                saveType: "copyrights",
                copyrights: {
                    en: this.copyrights,
                    ar: this.copyrightsArabic
                }
            };
            axios
                .post("/appDefaults", data)
                .then(response => {
                    this.editcopyrights = {
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
            this.$parent.toggleModule("copyrights");
        }
    },
    computed: {
        module() {
            return this.$parent.modules.copyrights;
        },
        copyrights() {
            return this.$parent.appDefaults.copyrights.en;
        },
        copyrightsArabic() {
            return this.$parent.appDefaults.copyrights.ar;
        }
    }
};
</script>