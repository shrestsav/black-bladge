<template>
    <div class="row">
        <div class="col">
            <div class="card" v-if="active.edit">
                <div class="row justify-content-center">
                    <div class="col-lg-3 order-lg-2">
                        <div class="card-profile-image">
                            <a href="#">
                                <img
                                    :src="vehicle.photo_src"
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
                    <h6 class="heading-small text-muted mb-4">EDIT Vehicle DATA</h6>
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
                                            v-model="vehicle[key]"
                                            class="form-control"
                                        />
                                        <date-picker
                                            :input-class="{'not-validated':errors[key]}"
                                            v-if="item['type']==='date'"
                                            v-model="vehicle[key]"
                                            lang="en"
                                            valuetype="format"
                                            input-class="form-control"
                                        ></date-picker>
                                        <textarea
                                            v-if="item['type']==='textarea'"
                                            rows="4"
                                            :class="{'not-validated':errors[key]}"
                                            class="form-control"
                                            placeholder="Write something about vehicle"
                                            v-model="vehicle[key]"
                                        ></textarea>
                                        <select
                                            class="form-control"
                                            v-if="item['type']==='select' && key==='gender'"
                                            v-model="vehicle[key]"
                                            :class="{'not-validated':errors[key]}"
                                        >
                                            <option value="Mr" selected>Mr</option>
                                            <option value="Mr">Mrs</option>
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
                            <h3 class="mb-0">Vechiles List</h3>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>S.No.</th>
                                <th>Vehicle Number</th>
                                <th>Brand</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr v-for="(item,key) in vehicles" :key="key">
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
                                                title="Edit Vehicle Information"
                                            >Edit Info</a>
                                        </div>
                                    </div>
                                </td>
                                <td>{{++key}}</td>
                                <td>{{item.vehicle_number}}</td>
                                <td>{{item.brand}}</td>
                                <td>{{item.description}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            fields: {},
            errors: {},
            active: {
                vehicle_id: "",
                page: "",
                select: false,
                selectedIds: [],
                edit: false
            },
            vehicle: {},
            vehicles: []
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "vehicles");
        this.$store.commit("changeCurrentMenu", "vehiclesMenu");
        this.getVehicles();
        this.defSettings();
    },
    methods: {
        defSettings() {
            axios
                .get("/getFields/createVehicle")
                .then(response => (this.fields = response.data));
        },
        getVehicles(page = 1) {
            this.active.page = page;
            axios.get("/vehicles?page=" + page).then(response => {
                this.vehicles = response.data;
            });
        },
        edit(key) {
            this.active.edit = true;
            this.vehicle = this.vehicles.data[key];
        },
        discardEdit() {
            this.active.edit = false;
            this.vehicle = {};
            this.getVehicles(this.active.page);
        },
        updateEditedData() {
            let formData = new FormData();
            for (var key in this.vehicle) {
                if (this.vehicle[key]) formData.append(key, this.vehicle[key]);
            }
            formData.append("_method", "patch");
            axios
                .post("/vehicles/" + this.vehicle.id, formData)
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
            this.vehicle.photo_file = e.target.files[0];
            this.vehicle.photo_src = URL.createObjectURL(
                this.vehicle.photo_file
            );
        },
        imageUrl() {
            return (
                window.location.origin +
                "/files/users/" +
                this.vehicle.id +
                "/" +
                this.vehicle.photo
            );
        }
    }
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