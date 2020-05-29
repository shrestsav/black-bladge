<template>
    <div class="vue-component-body">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <h6 class="text-light text-uppercase ls-1 mb-1">SELECT REPORT TYPE</h6>
                        <select class="form-control bg-transparent" v-model="report.type" @change="onReportTypeChange()">
                            <!-- <option value="total_sales">Total Sales Report</option> -->
                            <option value="driver">Driver Report</option>
                            <option value="customer">Customer Report</option>
                        </select>
                    </div>
                    <div class="col-3" v-if="report.type=='driver'">
                        <h6 class="text-light text-uppercase ls-1 mb-1">SELECT DRIVER</h6>
                        <select class="form-control bg-transparent" v-model="report.driver_id" @change="getDriversReport()">
                            <option :value="driver.id" v-for="(driver,index) in drivers" :key="index">{{driver.full_name}}</option>
                        </select>
                    </div>
                    <div class="col-3" v-if="report.type=='customer'">
                        <h6 class="text-light text-uppercase ls-1 mb-1">SELECT CUSTOMER</h6>
                        <select class="form-control bg-transparent" v-model="report.customer_id" @change="getCustomersReport()">
                            <option :value="customer.id" v-for="(customer,index) in customers" :key="index">{{customer.full_name}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <!-- <totalSales></totalSales> -->
                <driver :driver_id="report.driver_id" ref="driverReport"></driver>
                <customer :customer_id="report.customer_id" ref="customerReport"></customer>
            </div>
        </div>
    </div>
</template>


<script>
import { mapState } from "vuex";
import totalSales from "../charts/totalSales.vue";
import driver from "./driver.vue";
import customer from "./customer.vue";
export default {
    components: {
        totalSales,
        customer,
        driver,
    },
    data() {
        return {
            report: {
				type: "driver", //customer, driver
                driver_id:null,
                customer_id:null
            },
			errors: {},
            drivers:[],
            customers:[]
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "reports");
        this.$store.commit("changeCurrentMenu", "reportsMenu");
    },
    mounted() {
		this.$store.dispatch("getOrdersCount");
        
        //If refreshed the page
        if (this.$route.query.report_type != undefined) {
            this.report.type = this.$route.query.report_type
            if(this.$route.query.driver_id != undefined) {
                this.report.driver_id = this.$route.query.driver_id
                this.getDriversReport();
            }
            if(this.$route.query.customer_id != undefined) {
                this.report.customer_id = this.$route.query.customer_id
                this.getCustomersReport();
            }
        }

        this.onReportTypeChange();
    },
    methods: {
        goto() {
            this.$router.push({ name: "orders" });
		},
		getAllDrivers() {
            axios.get("/driver/all").then(response => {
                this.drivers = response.data;
            });
        },
        getAllCustomers(){
            axios.get('/customer/all').then(response => {
                this.customers = response.data;
            });
        },
		onReportTypeChange(){
            if(this.report.type=='driver')
                this.getAllDrivers();
            else if(this.report.type=='customer')
                this.getAllCustomers();
		},
		getDriversReport(){
            this.$router.replace({ 
                name: "reports", 
                query:{
                    report_type: this.report.type,
                    driver_id: this.report.driver_id
                }
            });
			this.$refs.driverReport.init(this.report.driver_id);
		},
		getCustomersReport(){
            this.$router.replace({ 
                name: "reports", 
                query:{
                    report_type: this.report.type,
                    customer_id: this.report.customer_id
                }
            });
			this.$refs.customerReport.init(this.report.customer_id);
		}
    },
    computed: {
        ...mapState(["ordersCount"])
    },
    watch: {}
};
</script>