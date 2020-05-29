<template>
    <div class="card" v-if="driver_id!=null && reportType=='driver'">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-3">
                    <h6 class="text-black text-uppercase ls-1 mb-1">Driver</h6>
                    <h5 class="h3 text-black mb-0">{{driver.full_name}}</h5>
                </div>
                <div class="col-7">
                    <div class="row">
                        <div class="col-3">
                            <h6 class="text-light text-uppercase ls-1 mb-1">View</h6>
                            <select class="form-control bg-transparent" v-model="reports.type">
                                <option value="monthly">Month</option>
                                <option value="yearly">Year</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <template v-if="reports.type=='monthly'">
                                <h6 class="text-light text-uppercase ls-1 mb-1">Select Month</h6>
                                <date-picker
                                    @change="driverOrders(1)"
                                    input-class="form-control bg-transparent"
                                    v-model="reports.year_month"
                                    lang="en"
                                    type="month"
                                    format="YYYY-MM"
                                    value-type="format"
                                ></date-picker>
                            </template>
                            <template v-if="reports.type=='yearly'">
                                <h6 class="text-light text-uppercase ls-1 mb-1">Select Year</h6>
                                <date-picker
                                    @change="driverOrders(1)"
                                    input-class="form-control bg-transparent"
                                    v-model="reports.year"
                                    lang="en"
                                    type="year"
                                    format="YYYY"
                                    value-type="format"
                                ></date-picker>
                            </template>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-2 text-right">
                    <a
                        :href="origin_url+'/reports/export?report=driverOrders&driver_id='+reports.driver_id+'&type='+reports.type+'&year='+reports.year+'&year_month='+reports.year_month"
                        target="_blank"
                    >
                        <button type="button" class="btn btn-success btn-sm">
                            Export
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </a>
                </div>-->
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th></th>
                        <th>S.No.</th>
                        <th>Type</th>
                        <th>Ordered Date</th>
                        <th>Actual Picked Date</th>
                        <th>Actual Dropped Date</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody class="list">
                    <tr v-for="(item,key) in orders" :key="key">
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
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a
                                        class="dropdown-item"
                                        href="javascript:;"
                                        @click="$router.push({ name: 'orderDetails',query: { orderID: item.id } })"
                                        data-toggle="modal"
                                        data-target="#orderDetails"
                                        title="Show Order Details"
                                    >Details</a>
                                </div>
                            </div>
                        </td>
                        <td>{{key+1}}</td>
                        <td>{{item.order_type}}</td>
                        <td>{{dateTimeWithTz(item.booked_at)}}</td>
                        <td>{{dateTimeWithTz(item.actual_picked_timestamp)}}</td>
                        <td>{{dateTimeWithTz(item.actual_dropped_timestamp)}}</td>
                        <td>{{item.customer_full_name}}</td>
                        <td>{{item.status_str}}</td>
                        <td>{{item.pricing_unit}} {{item.grand_total}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            <div class="row">
                <div class="col-8">
                    <!-- <pagination :data="orders" @pagination-change-page="driverOrders"></pagination> -->
                </div>
                <div class="col-2">
                    <h6 class="text-black text-uppercase ls-1 mb-1">Total Order</h6>
                    <h5 class="h3 text-black mb-0">{{orders.length}}</h5>
                </div>
                <div class="col-2">
                    <h6 class="text-black text-uppercase ls-1 mb-1">Total Amount</h6>
                    <h5 class="h3 text-black mb-0">{{pricingUnit}} {{grandTotalAll}}</h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
export default {
    components: {
        DatePicker
    },
    props: ["driver_id"],
    data() {
        return {
            reports: {
                driver_id: "",
                page: "",
                type: "",
                year: "",
                year_month: ""
            },
            driver:{},
            drivers: [],
            orders: [],
            orderStatus: [],
            origin_url: window.location.origin,
            pricingUnit: ""
        };
    },
    methods: {
        init(driver_id) {
            this.orders = [];
            var today = new Date();
            this.reports = {
                driver_id: driver_id,
                page: "",
                year: "",
                type: "monthly",
                year_month:
                    today.getFullYear() + "-" + (Number(today.getMonth()) + 1)
            };
            this.driverOrders();
        },
        hideModal() {
            this.$refs["driverOders"].hide();
            this.reports.driver_id = "";
        },
        dateTimeWithTz(date) {
            if (date) {
                var date = new Date(date);
                return this.$moment(date).format(
                    "ddd MMM DD YYYY [at] HH:mm A"
                );
            } else return " - ";
        },
        dateTime(date) {
            if (date) {
                var date = new Date(date + " UTC");
                return this.$moment(date).format(
                    "ddd MMM DD YYYY [at] HH:mm A"
                );
            } else return " - ";
        },
        driverOrders(page = 1) {
            axios
                .get(
                    "/reports/driver/orders/" +
                        this.reports.driver_id +
                        "?page=" +
                        page +
                        "&type=" +
                        this.reports.type +
                        "&year=" +
                        this.reports.year +
                        "&year_month=" +
                        this.reports.year_month
                )
                .then(response => {
                    this.orders = response.data.data;
                    this.pricingUnit = response.data.meta.pricing_unit;
                    this.driver = response.data.meta.driver;
                });
        }
    },
    computed: {
        grandTotalAll() {
            var data = this.orders;
            var filtered = data.filter(item => {
                return item.grand_total;
            });
            var total = filtered.reduce((currentTotal, item) => {
                return item.grand_total + currentTotal;
            }, 0);

            return total;
        },
        reportType(){
            return this.$parent.report.type
        }
    }
};
</script>