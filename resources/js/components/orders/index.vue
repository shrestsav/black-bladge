<template>
    <div class="vue-component-body">
        <div class="row align-items-center py-4">
            <div class="col text-right">
                <a
                    href="javascript:;"
                    class="btn btn-sm btn-danger"
                    @click="cancelOrders"
                    v-if="
                        pick.orderIds.length &&
                            active.status != 'Cancelled' &&
                            pick.orders
                    "
                    >Cancel Booking</a
                >
                <a
                    href="javascript:;"
                    class="btn btn-sm btn-danger"
                    @click="deleteOrders"
                    v-if="pick.orderIds.length && pick.orders"
                    >Delete Booking</a
                >
                <a
                    href="javascript:;"
                    class="btn btn-sm btn-neutral"
                    @click="pick.orders = !pick.orders"
                    v-if="!pick.orders"
                    >Select</a
                >
                <a
                    href="javascript:;"
                    class="btn btn-sm btn-info"
                    @click="pick.orders = !pick.orders"
                    v-if="pick.orders"
                    >Cancel</a
                >
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="nav-wrapper">
                            <ul
                                class="nav nav-pills nav-fill flex-column flex-md-row"
                                id="tabs-icons-text"
                                role="tablist"
                            >
                                <li
                                    class="nav-item"
                                    v-for="(status, key) in orderStatus"
                                    :key="key"
                                >
                                    <a
                                        class="nav-link mb-sm-3 mb-md-0"
                                        :class="
                                            key == $route.query.goto
                                                ? 'active'
                                                : ''
                                        "
                                        :id="key"
                                        data-toggle="tab"
                                        href
                                        role="tab"
                                        aria-controls="tabs-icons-text-1"
                                        aria-selected="false"
                                        @click="getOrders(key)"
                                    >
                                        {{ key }}
                                        <span
                                            class="status_count"
                                            v-if="ordersCount[key]"
                                            >{{ ordersCount[key] }}</span
                                        >
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th></th>
                                    <th>S.No.</th>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Order Type</th>
                                    <th>Pickup From</th>
                                    <th>Pickup Date</th>
                                    <th>Picked By</th>
                                    <th v-if="checkStatus()">Vechile Info</th>
                                    <th>Status</th>
                                    <th>Ordered</th>
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
                                            v-model="search.orderID"
                                            @change="searchOrder"
                                            type="text"
                                            placeholder="ID"
                                            class="form-control searchRow"
                                        />
                                    </th>
                                    <th>
                                        <input
                                            v-model="search.customer"
                                            @change="searchOrder"
                                            type="text"
                                            placeholder="Customer Name"
                                            class="form-control searchRow"
                                        />
                                    </th>
                                    <th>
                                        <select
                                            v-model="search.type"
                                            @change="searchOrder"
                                            class="form-control searchRow"
                                        >
                                            <option value selected>Both</option>
                                            <option value="1">Instant</option>
                                            <option value="2">Advance</option>
                                        </select>
                                    </th>
                                    <th>
                                        <input
                                            v-model="search.pick_location"
                                            @change="searchOrder"
                                            type="text"
                                            placeholder="Address"
                                            class="form-control searchRow"
                                        />
                                    </th>
                                    <th>
                                        <input
                                            v-model="search.pick_date"
                                            @change="searchOrder"
                                            type="date"
                                            placeholder="Address"
                                            class="form-control searchRow"
                                        />
                                    </th>
                                    <th>
                                        <input
                                            v-model="search.pick_driver"
                                            @change="searchOrder"
                                            type="text"
                                            placeholder="Driver Name"
                                            class="form-control searchRow"
                                        />
                                    </th>
                                    <th v-if="checkStatus()">
                                        <select
                                            v-model="search.vehicle_id"
                                            @change="searchOrder"
                                            class="form-control searchRow"
                                        >
                                            <template
                                                v-for="(vehicle,
                                                index) in vehicles"
                                            >
                                                <option
                                                    v-bind:value="vehicle.id"
                                                    :key="index"
                                                    >{{
                                                        vehicle.vehicle_number
                                                    }}</option
                                                >
                                            </template>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr
                                    v-for="(item, index) in showOrders.data"
                                    :key="index"
                                    v-bind:class="{
                                        urgent: checkPending(index)
                                    }"
                                >
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
                                                <i
                                                    class="fas fa-ellipsis-v"
                                                ></i>
                                            </a>
                                            <div
                                                class="dropdown-menu dropdown-menu-right dropdown-menu-arrow"
                                            >
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:;"
                                                    @click="details(item.id)"
                                                    data-toggle="modal"
                                                    data-target="#orderDetails"
                                                    title="Show Order Details"
                                                    >Details</a
                                                >

                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:;"
                                                    @click="
                                                        assign(
                                                            index,
                                                            'pickAssign'
                                                        )
                                                    "
                                                    data-toggle="modal"
                                                    data-target="#assignOrder"
                                                    title="Assign Pending Order"
                                                    v-if="item.status == 0"
                                                    >Assign Driver</a
                                                >
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="custom-control custom-checkbox"
                                            v-if="pick.orders"
                                        >
                                            <input
                                                class="custom-control-input"
                                                :id="'check_' + index"
                                                type="checkbox"
                                                :checked="
                                                    pick.orderIds.includes(
                                                        item.id
                                                    )
                                                "
                                                @change="
                                                    pickMultipleOrders(
                                                        item.id,
                                                        $event
                                                    )
                                                "
                                            />
                                            <label
                                                class="custom-control-label"
                                                :for="'check_' + index"
                                            ></label>
                                        </div>
                                        <span v-else>{{ index + 1 }}</span>
                                    </td>
                                    <td>BOK-{{ item.id }}</td>
                                    <td>{{ item.customer_full_name }}</td>
                                    <td>{{ item.order_type }}</td>
                                    <td v-if="item.pick_location">
                                        {{ item.pick_location.name }}
                                    </td>
                                    <td>{{ item.pick_timestamp }}</td>
                                    <td>
                                        <span v-if="item.status === 0"
                                            >Not Assigned</span
                                        >
                                        <span v-if="item.status > 0">{{
                                            item.driver_full_name
                                        }}</span>
                                    </td>
                                    <td v-if="checkStatus()">
                                        {{ item.vehicle_number }}
                                    </td>
                                    <td>
                                        <span>{{ item.status_str }}</span>
                                    </td>
                                    <td>{{ dateDiff(item.booked_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        <pagination
                            :data="showOrders"
                            @pagination-change-page="getResults"
                        ></pagination>
                    </div>
                </div>
            </div>
            <assign :active="active" v-if="showAssign" ref="assign"></assign>
        </div>
    </div>
</template>

<script>
import assign from "./assign.vue";
import { settings } from "../../config/settings";
import { mapState } from "vuex";
import DatePicker from "vue2-datepicker";

export default {
    components: {
        assign,
        DatePicker
    },
    data() {
        return {
            search: {
                type: ""
            },
            active: {
                order: "",
                page: 1,
                order_id: "",
                status: "Active"
            },
            showAssign: true,
            showDetails: true,
            pick: {
                orders: false,
                orderIds: []
            },
            errors: {},
            message: "",
            showOrders: {},
            vehicles: []
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "orders");
        this.$store.commit("changeCurrentMenu", "ordersMenu");
        // get pending orders on page load
        this.$store.dispatch("getOrderStatus");
        // get all vehicles info
        this.getVehicles();

        if (this.$route.query.goto) this.getOrders(this.$route.query.goto);
        else this.getOrders("Active");
    },
    mounted() {},
    methods: {
        getOrders(status = "Active") {
            this.active.status = status;
            this.getResults();
            this.pick.orderIds = [];
            this.$store.dispatch("getOrdersCount");
            this.$router.replace({
                name: "orders",
                query: {
                    goto: status
                }
            });
        },
        getVehicles() {
            axios.get("/vehicle/all").then(response => {
                this.vehicles = response.data;
            });
        },
        getResults(page = 1) {
            this.active.page = page;
            this.$store.dispatch("getOrders", this.active);
        },
        getStatus(status) {
            return settings.orderStatuses[status];
        },
        getOrderType(type) {
            return settings.orderType[type];
        },
        details(id) {
            this.$router.push({ name: "orderDetails", query: { orderID: id } });
        },
        assign(index, type) {
            this.active.order = index;
            this.active.type = type;
            this.showAssign = true;
            this.$refs.assign.mount();
        },
        dateDiff(date) {
            var date = new Date(date);
            return this.$moment(date).fromNow(); // a
        },
        checkPending(index) {
            const date = this.orders.data[index].created_at;
            const createdAt = this.$moment(new Date(date + " UTC"));
            const currentTime = this.$moment(Date.now());
            const passed_minute = Math.abs(
                createdAt.diff(currentTime, "minutes")
            );
            if (this.orders.data[index].status == 0 && passed_minute >= 10)
                return true;
            else return false;
        },
        searchOrder() {
            axios
                .post("/orders/search/" + this.active.status, this.search)
                .then(response => {
                    this.$store.commit("setOrders", response.data);
                });
        },
        triggerPickOrders() {
            this.pick.orders = !this.pick.orders;
        },
        pickMultipleOrders(id, event) {
            if (event.target.checked) {
                if (!this.pick.orderIds.includes(id))
                    this.pick.orderIds.push(id);
            } else if (!event.target.checked) {
                if (this.pick.orderIds.includes(id)) {
                    var index = this.pick.orderIds.indexOf(id);
                    this.pick.orderIds.splice(index, 1);
                }
            }
        },
        cancelOrders() {
            this.$swal({
                title: "Are you sure?",
                text: "Orders will be cancelled !!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes!"
            }).then(result => {
                if (result.value) {
                    axios
                        .post("/cancelMultipleOrders", this.pick)
                        .then(response => {
                            this.getOrders(this.active.status);
                            showNotify("success", response.data.message);
                        })
                        .catch(error => {
                            showNotify("danger", error.response.data.message);
                        });
                }
            });
        },
        deleteOrders() {
            this.$swal({
                title: "Are you sure?",
                text: "Orders will be permanently deleted !!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    axios
                        .post("/deleteMultipleOrders", this.pick)
                        .then(response => {
                            this.getOrders(this.active.status);
                            showNotify("success", response.data.message);
                        })
                        .catch(error => {
                            showNotify("danger", error.response.data.message);
                        });
                }
            });
        },
        checkStatus() {
            return (
                this.active.status !== "Active" &&
                this.active.status !== "Unassigned"
            );
        }
    },
    computed: {
        pending() {
            return this.orderStatus["Pending"];
        },
        received() {
            return this.orderStatus["Received"];
        },
        ready_for_delivery() {
            return this.orderStatus["Ready for Delivery"];
        },
        on_hold() {
            return this.orderStatus["On Hold"];
        },
        completed() {
            return this.orderStatus["Completed"];
        },
        ...mapState(["orders", "orderStatus", "ordersCount"])
    },
    watch: {
        orders(value) {
            this.showOrders = value;
        }
    }
};
</script>

<style type="text/css" scoped>
.urgent {
    background: #ffd7d47d;
    color: #853737;
}
</style>
