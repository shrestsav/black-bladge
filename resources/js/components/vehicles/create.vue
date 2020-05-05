<template>
    <div class="card">
        <div class="row justify-content-center">
            <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                    <a href="#">
                        <img :src="vehicle.photo_src" class="rounded-circle" @click="triggerDPInput" />
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
                                    v-if="item['type']==='text' || item['type']==='number'"
                                    :class="{'not-validated':errors[key]}"
                                    :type="item['type']"
                                    :id="'input-'+key"
                                    :placeholder="item['display_name']"
                                    v-model="vehicle[key]"
                                    class="form-control"
                                />
                                <textarea
                                    v-if="item['type']==='textarea'"
                                    rows="4"
                                    :class="{'not-validated':errors[key]}"
                                    class="form-control"
                                    placeholder="Write something about Vehicle"
                                    v-model="vehicle[key]"
                                ></textarea>
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
            vehicle: {},
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "createVehicle");
        this.$store.commit("changeCurrentMenu", "vehiclesMenu");
    },
    mounted() {
        this.defSettings();
        this.init();
    },
    methods: {
        init() {
            this.vehicle = {
                vehicle_number: "",
                brand: "",
                description: "",
				photo_src: "/files/images/vehicle.webp"
            };
        },
        defSettings() {
            axios
                .get("/getFields/createVehicle")
                .then(response => (this.fields = response.data));
        },
        save() {
            let formData = new FormData();
            for (var key in this.vehicle) {
                if (this.vehicle[key]) formData.append(key, this.vehicle[key]);
            }
            axios
                .post("/vehicles", formData)
                .then(response => {
                    this.errors = {};
                    showNotify("success", "Vehicle Added");
                    this.init();
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        triggerDPInput() {
            this.$refs.dp_photo.click();
        },
        imageChange(e) {
            this.vehicle.photo_file = e.target.files[0];
            this.vehicle.photo_src = URL.createObjectURL(this.vehicle.photo_file);
        },
    }
};
</script>
