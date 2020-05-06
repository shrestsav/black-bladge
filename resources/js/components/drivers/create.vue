<template>
    <div class="card">
        <div class="row justify-content-center">
            <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                    <a href="#">
                        <img :src="driver.photo_src" class="rounded-circle" @click="triggerDPInput" />
                        <input
                            type="file"
                            class="custom-file-input"
                            lang="en"
                            v-on:change="imageChange"
                            style="display: none;"
                            ref="dp_photo"
                        />
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div v-for="(section,sec_name,index) in fields" :key="index">
                <h6 class="heading-small text-muted mb-4">{{sec_name}}</h6>
                <div class="pl-lg-4">
                    <div class="row">
                        <div
                            :class="'col-lg-'+item['col']"
                            v-for="(item,key) in section"
                            :key="key"
                        >
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    :for="'input-'+key"
                                >{{item['display_name']}}</label>
                                <input
                                    v-if="item['type']==='text' || item['type']==='number' || item['type']==='email'"
                                    :class="{'not-validated':errors[key]}"
                                    :type="item['type']"
                                    :id="'input-'+key"
                                    :placeholder="item['display_name']"
                                    v-model="driver[key]"
                                    class="form-control"
                                />
                                <date-picker
                                    :input-class="{'not-validated':errors[key]}"
                                    v-if="item['type']==='date'"
                                    v-model="driver[key]"
                                    lang="en"
                                    valuetype="format"
                                    input-class="form-control"
                                ></date-picker>
                                <textarea
                                    v-if="item['type']==='textarea'"
                                    rows="4"
                                    :class="{'not-validated':errors[key]}"
                                    class="form-control"
                                    placeholder="Write something about driver"
                                    v-model="driver[key]"
                                ></textarea>
                                <select
                                    class="form-control"
                                    v-if="item['type']==='select' && key==='gender'"
                                    v-model="driver[key]"
                                    :class="{'not-validated':errors[key]}"
                                >
                                    <option value="Mr" selected>Mr</option>
                                    <option value="Mr">Mrs</option>
                                </select>
                                <select
                                    class="form-control"
                                    v-if="item['type']==='select' && key==='vehicle_id'"
                                    v-model="driver[key]"
                                    :class="{'not-validated':errors[key]}"
                                >
                                    <option value="" selected disabled>Select Vehicle</option>
                                    <option :value="item.id" v-for="(item,index) in vehicles" :key="index">{{item.vehicle_number}}</option>
                                </select>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors[key]"
                                >{{errors[key][0]}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4" />
            </div>
        </div>
        <div class="card-footer text-center">
            <button class="btn btn-outline-info" @click="save">Create</button>
        </div>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";

export default {
    components: {
        DatePicker
    },
    data() {
        return {
            fields: {},
            driver: {},
            errors: {},
            vehicles: []
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "createDriver");
        this.$store.commit("changeCurrentMenu", "driversMenu");
    },
    mounted() {
        this.defSettings();
        this.init();
    },
    methods: {
        init() {
            this.driver = {
                username: "",
                license_no: "",
                gender: "Mr",
                fname: "",
                lname: "",
                phone: "",
                email: "",
                dob: "",
				country: "",
				photo_src: "/files/images/person.png"
            };
            this.getVehicles();
        },
        defSettings() {
            axios
                .get("/getFields/createUser")
                .then(response => (this.fields = response.data));
        },
        save() {
            let formData = new FormData();
            for (var key in this.driver) {
                if (this.driver[key]) formData.append(key, this.driver[key]);
            }
            axios
                .post("/drivers", formData)
                .then(response => {
                    this.errors = {};
                    showNotify("success", "Driver has been created");
                    this.init();
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        getVehicles()
        {
            axios.get('/vehicles').then((response) => {
                this.vehicles = response.data;
            })
        },
        triggerDPInput() {
            this.$refs.dp_photo.click();
        },
        imageChange(e) {
            this.driver.photo_file = e.target.files[0];
            this.driver.photo_src = URL.createObjectURL(this.driver.photo_file);
        },
    }
};
</script>
