<template>
    <div class="row">
        <div class="col">
            <div class="card" v-if="active.edit">
                <div class="row justify-content-center">
                    <div class="col-lg-3 order-lg-2">
                        <div class="card-profile-image">
                            <a href="#">
                                <img
                                    :src="driver.photo_src"
                                    class="rounded-circle"
                                    @click="triggerDPInput"
                                />
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
                    <h6 class="heading-small text-muted mb-4">EDIT DRIVER DATA</h6>
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
                                            <option value selected disabled>Select Vehicle</option>
                                            <option
                                                :value="item.id"
                                                v-for="(item,index) in vehicles"
                                                :key="index"
                                            >{{item.vehicle_number}}</option>
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
                    <div class="float-right">
                        <button
                            type="button"
                            class="btn btn-success btn-sm"
                            @click="updateEditedData()"
                        >Update</button>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            @click="discardEdit()"
                        >Cancel</button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Drivers List</h3>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>S.No.</th>
                                <th>Name</th>
                                <th>License No</th>
                                <th>Driver ID</th>
                                <th>Vehicle Number</th>
                                <th>Contact</th>
                            </tr>
                            <tr>
                                <th>
                                    <div
                                        class="icon icon-shape bg-gradient-red text-white rounded-circle shadow"
                                    >
                                        <i class="fas fa-search"></i>
                                    </div>
                                </th>
                                <th></th>
                                <th>
                                    <input
                                        v-model="search.full_name"
                                        @change="getDrivers(1)"
                                        type="text"
                                        placeholder="ID"
                                        class="form-control searchRow"
                                    />
                                </th>
                                <th>
                                    <input
                                        v-model="search.license_no"
                                        @change="getDrivers(1)"
                                        type="text"
                                        placeholder="Customer Name"
                                        class="form-control searchRow"
                                    />
                                </th>
                                <th>
                                    <input
                                        v-model="search.username"
                                        @change="getDrivers(1)"
                                        type="text"
                                        placeholder="ID"
                                        class="form-control searchRow"
                                    />
                                </th>
                                <th>
                                    <select
                                        v-model="search.vehicle_id"
                                        @change="getDrivers(1)"
                                        class="form-control searchRow"
                                    >
                                        <option :value="vehicle.id" v-for="(vehicle,index) in vehicles" :key="index">{{vehicle.vehicle_number}}</option>
                                    </select>
                                </th>
                                <th>
                                    <input
                                        v-model="search.contact"
                                        @change="getDrivers(1)"
                                        type="text"
                                        placeholder="Address"
                                        class="form-control searchRow"
                                    />
                                </th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr v-for="(item,key) in drivers.data" :key="key">
                                <td>
                                    <div class="dropdown">
                                        <a
                                            class="btn btn-sm btn-icon-only text-light"
                                            href="javascript:;"
                                            role="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div
                                            class="dropdown-menu dropdown-menu-right dropdown-menu-arrow"
                                        >
                                            <a
                                                class="dropdown-item"
                                                href="javascript:;"
                                                @click="edit(key-1)"
                                                title="Edit Driver Information"
                                            >Edit Info</a>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:;"
                                                @click="driverOrders(item.id)"
                                                title="Edit Driver Information"
                                            >View Orders</a>
                                        </div>
                                    </div>
                                </td>
                                <td>{{++key}}</td>
                                <td>{{item.full_name}}</td>
                                <td>{{item.license_no}}</td>
                                <td>{{item.username}}</td>
                                <td>{{item.vehicle_number}}</td>
                                <td>{{item.phone}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4" v-if="drivers.data">
                    <pagination :data="drivers" @pagination-change-page="getDrivers"></pagination>
                </div>
            </div>
            <orderReport ref="driverOrderModal"></orderReport>
        </div>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import orderReport from "./orderReport.vue";

export default {
    components: {
        DatePicker,
        orderReport
    },
    data() {
        return {
            search:{
                full_name: "",
                license_no: "",
                username: "",
                phone: "",
                vehicle_id: ""
            },
            fields: {},
            errors: {},
            active: {
                driver_id: "",
                page: "",
                select: false,
                selectedIds: [],
                edit: false
            },
            driver: {},
			drivers: [],
			vehicles:[]
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "drivers");
        this.$store.commit("changeCurrentMenu", "driversMenu");
		this.getDrivers();
		this.getVehicles();
        this.defSettings();
    },
    methods: {
        defSettings() {
            axios
                .get("/getFields/createUser")
                .then(response => (this.fields = response.data));
        },
        getDrivers(page = 1) {
            var search = this.search;
            var hitURL = "/drivers?page=" + page +
                         "&full_name=" + search.full_name +
                         "&license_no=" + search.license_no +
                         "&username=" + search.username +
                         "&phone=" + search.phone +
                         "&vehicle_id=" + search.vehicle_id;
            this.active.page = page;
            axios.get(hitURL).then(response => {
                this.drivers = response.data;
            });
        },
        dateTime(date) {
            if (date) {
                var date = new Date(date + " UTC");
                return this.$moment(date).format(
                    "ddd MMM DD YYYY [at] HH:mm A"
                );
            } else return " - ";
        },
        hideModal() {
            this.$refs["driverOders"].hide();
            this.active.driver_id = "";
        },
        driverOrders(id) {
            this.$router.push({ 
                name: "reports", 
                query:{
                    report_type: 'driver',
                    driver_id: id
                }
            });
            // this.$refs.driverOrderModal.init(id);
            // this.active.driver_id = id;
        },
        edit(key) {
            this.active.edit = true;
            this.driver = this.drivers.data[key];
        },
        discardEdit() {
            this.active.edit = false;
            this.driver = {};
            this.getDrivers(this.active.page);
		},
		getVehicles()
        {
            axios.get('/vehicles').then((response) => {
                this.vehicles = response.data;
            })
        },
        updateEditedData() {
            let formData = new FormData();
            for (var key in this.driver) {
                if (this.driver[key]) formData.append(key, this.driver[key]);
            }
            formData.append("_method", "patch");
            axios
                .post("/drivers/" + this.driver.id, formData)
                .then(response => {
                    this.discardEdit();
                    showNotify("success", response.data.message);
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
            this.driver.photo_file = e.target.files[0];
            this.driver.photo_src = URL.createObjectURL(this.driver.photo_file);
        },
        imageUrl() {
            return (
                window.location.origin +
                "/files/users/" +
                this.driver.id +
                "/" +
                this.driver.photo
            );
        }
    },
};
</script>

<style type="text/css">
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
#driverOrders .modal-dialog {
    max-width: 90%;
}
#driverOrders___BV_modal_body_ {
    padding: 0;
}
#driverOrders___BV_modal_body_ .card {
    margin-bottom: 0;
}
</style>