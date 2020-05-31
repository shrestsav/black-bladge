<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-wrapper">
                <general></general>
                <!-- <offers></offers> -->
                <!-- <order></order>
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col">
                        <h3 class="mb-0">Support Page</h3>
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-sm" @click="toggleModule('supportPage')">{{modules.supportPage.icon}}</button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body" v-if="modules.supportPage.display">
                    <div class="row">
                      <div class="col-md-4">
                        <img :src="appDefaults.company_logo_url" class="img-center img-fluid" style="height: 109px;">
                        <br>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" lang="en" ref="file" v-on:change="logoFileUpload()">
                          <label class="custom-file-label">Company Logo</label>
                        </div>                
                      </div>
                      <div class="col-md-8">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label">Company Email</label>
                              <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input class="form-control" type="text" v-model="appDefaults.company_email">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label">FAQ Link</label>
                              <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input class="form-control" type="text" v-model="appDefaults.FAQ_link">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label">Online Chat</label>
                              <div class="row">
                                <div class="col-md-5">
                                  <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    </div>
                                    <input class="form-control" type="text" v-model="appDefaults.online_chat.time">
                                  </div>
                                </div>
                                <div class="col-md-7">
                                  <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input class="form-control" type="text" v-model="appDefaults.online_chat.url">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label">Hotline Contact No</label>
                              <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input class="form-control" type="text" v-model="appDefaults.hotline_contact">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="float-right">
                      <button class="btn btn-outline-primary" @click="save('supportSetting')">Save</button>
                    </div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col">
                        <h3 class="mb-0">Main Areas</h3>
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-sm" @click="toggleModule('mainArea')">{{modules.mainArea.icon}}</button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body" v-if="modules.mainArea.display">
                    <div class="row">
                      <div class="bootstrap-tagsinput">
                        <span class="tag badge badge-primary" v-for="(item,index) in mainAreas" :key="index">
                          {{item.name}}
                          <span data-role="remove" @click="deleteArea(item.id)"></span>
                        </span>
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="form-control-label">Area Name</label>
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                            </div>
                            <input :class="{'not-validated':errors.name}" class="form-control" type="text" v-model="mainArea.name">
                          </div>
                          <div class="invalid-feedback" style="display: block;" v-if="errors.name">
                            {{errors.name[0]}}
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="float-right">
                      <button class="btn btn-outline-primary" @click="saveMainArea()">Create</button>
                    </div>
                  </div>
                </div>-->
                <!-- <TACS></TACS> -->

                <div class="row">
                    <div class="col-md-6">
                        <FAQSEnglish></FAQSEnglish>
                    </div>
                    <div class="col-md-6">
                        <FAQSArabic></FAQSArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <TACSEnglish></TACSEnglish>
                    </div>
                    <div class="col-md-6">
                        <TACSArabic></TACSArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <copyrightEnglish></copyrightEnglish>
                    </div>
                    <div class="col-md-6">
                        <copyrightArabic></copyrightArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <dataProvidersEnglish></dataProvidersEnglish>
                    </div>
                    <div class="col-md-6">
                        <dataProvidersArabic></dataProvidersArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <localInformationEnglish></localInformationEnglish>
                    </div>
                    <div class="col-md-6">
                        <localInformationArabic></localInformationArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <privacyPolicyEnglish></privacyPolicyEnglish>
                    </div>
                    <div class="col-md-6">
                        <privacyPolicyArabic></privacyPolicyArabic>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <softwareLicencesEnglish></softwareLicencesEnglish>
                    </div>
                    <div class="col-md-6">
                        <softwareLicencesArabic></softwareLicencesArabic>
                    </div>
                </div>
                <!-- <orderTypesDesc></orderTypesDesc> -->
            </div>
        </div>
    </div>
</template>

<script>
import general from "./general.vue";
import order from "./order.vue";
import offers from "./offers.vue";
import TACSEnglish from "./TACSEnglish.vue";
import TACSArabic from "./TACSArabic.vue";
import FAQSEnglish from "./FAQSEnglish.vue";
import FAQSArabic from "./FAQSArabic.vue";
import copyrightArabic from "./copyrightArabic.vue";
import copyrightEnglish from "./copyrightEnglish.vue";
import dataProvidersArabic from "./dataProvidersArabic.vue";
import dataProvidersEnglish from "./dataProvidersEnglish.vue";
import localInformationArabic from "./localInformationArabic.vue";
import localInformationEnglish from "./localInformationEnglish.vue";
import privacyPolicyArabic from "./privacyPolicyArabic.vue";
import privacyPolicyEnglish from "./privacyPolicyEnglish.vue";
import softwareLicencesArabic from "./softwareLicencesArabic.vue";
import softwareLicencesEnglish from "./softwareLicencesEnglish.vue";
import orderTypesDesc from "./orderTypesDesc.vue";
import { mapState } from "vuex";

export default {
    components: {
        general,
        order,
        offers,
        orderTypesDesc,
        TACSEnglish,
        TACSArabic,
        FAQSEnglish,
        FAQSArabic,
        copyrightEnglish,
        copyrightArabic,
        dataProvidersArabic,
        dataProvidersEnglish,
        localInformationArabic,
        localInformationEnglish,
        privacyPolicyArabic,
        privacyPolicyEnglish,
        softwareLicencesArabic,
        softwareLicencesEnglish
    },
    data() {
        return {
            modules: {
                general: {
                    display: true,
                    icon: "-"
                },
                supportPage: {
                    display: false,
                    icon: "+"
                },
                order: {
                    display: false,
                    icon: "+"
                },
                mainArea: {
                    display: false,
                    icon: "+"
                },
                offers: {
                    display: false,
                    icon: "+"
                },
                TACS: {
                    display: false,
                    icon: "+"
                },
                FAQS: {
                    display: false,
                    icon: "+"
                },
                copyrights: {
                    display: false,
                    icon: "+"
                },
                data_providers: {
                    display: false,
                    icon: "+"
                },
                local_informations: {
                    display: false,
                    icon: "+"
                },
                privacy_policies: {
                    display: false,
                    icon: "+"
                },
                software_licences: {
                    display: false,
                    icon: "+"
                },
                orderTypesDesc: {
                    display: false,
                    icon: "+"
                }
            },
            mainArea: {},
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "appDefaults");
        this.$store.commit("changeCurrentMenu", "appDefaultsMenu");
        this.$store.dispatch("getAppDefaults");
        this.$store.dispatch("getMainAreas");
    },
    mounted() {},
    methods: {
        save(part) {
            let formData = new FormData();

            formData.append("saveType", part);
            for (var key in this.appDefaults) {
                formData.append(key, this.appDefaults[key]);
            }
            formData.append(
                "order_time",
                JSON.stringify(this.appDefaults.order_time)
            );
            formData.append(
                "online_chat",
                JSON.stringify(this.appDefaults.online_chat)
            );
            formData.append(
                "driver_notes",
                JSON.stringify(this.appDefaults.driver_notes)
            );

            axios
                .post("/appDefaults", formData)
                .then(response => {
                    console.log(response);
                    showNotify("success", "App Default has been created");
                })
                .catch(error => {
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        saveMainArea() {
            axios
                .post("/mainArea", this.mainArea)
                .then(response => {
                    console.log(response.data);
                    this.errors = {};
                    this.mainArea = {};
                    this.$store.dispatch("getMainAreas");
                    showNotify("success", response.data);
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        deleteArea(id) {
            this.$swal({
                title: "Are you sure?",
                text: "This may mess things up for customers!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    axios
                        .delete("/mainArea/" + id)
                        .then(response => {
                            this.$store.dispatch("getMainAreas");
                            showNotify("success", response.data);
                        })
                        .catch(error => {
                            showNotify("danger", error.response.data.errors);
                        });
                }
            });
        },
        logoFileUpload() {
            this.appDefaults.logoFile = this.$refs.file.files[0];
            this.appDefaults.company_logo_url = URL.createObjectURL(
                this.appDefaults.logoFile
            );
        },
        toggleModule(module) {
            if (this.modules[module].display) {
                this.modules[module].display = false;
                this.modules[module].icon = "+";
            } else {
                this.modules[module].display = true;
                this.modules[module].icon = "-";
            }
            for (var mod in this.modules) {
                if (mod != module) {
                    this.modules[mod].display = false;
                    this.modules[mod].icon = "+";
                }
            }
        }
    },
    computed: {
        ...mapState(["appDefaults", "mainAreas"])
    }
};
</script>

<style>
.mx-datepicker {
    width: unset;
    display: unset;
}
.mx-datepicker-popup {
    top: 0 !important;
}
.not-validated {
    border-color: #fb6340;
}
.form-control .vs__dropdown-toggle {
    border: 0px !important;
}
.table td,
.table th {
    padding: 0.8rem;
}
.nav-pills .nav-item:not(:last-child) {
    padding-right: unset;
}
.nav-pills .nav-link {
    border-radius: unset;
}
.nav-wrapper {
    padding: unset;
}
.status_count {
    padding: 2px 7px;
    border-radius: 17px;
    background: #f44336;
    position: relative;
    left: 25px;
    color: white;
}
.notification_count {
    padding: 1px 7px 6px 7px;
    border-radius: 17px;
    background: #7d8a92;
    position: relative;
    top: -7px;
}
</style>