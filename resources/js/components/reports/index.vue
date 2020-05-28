<template>
    <div class="vue-component-body">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <h6 class="text-light text-uppercase ls-1 mb-1">SELECT REPORT TYPE</h6>
                        <select class="form-control bg-transparent" v-model="report.type">
                            <!-- <option value="total_sales">Total Sales Report</option> -->
                            <option value="driver">Driver Report</option>
                        </select>
                    </div>
                    <div class="col-3" v-if="report.type=='driver'">
                        <h6 class="text-light text-uppercase ls-1 mb-1">SELECT DRIVER</h6>
                        <select class="form-control bg-transparent" v-model="report.driver_id" @change="getDriversReport()">
                            <option :value="driver.id" v-for="(driver,index) in drivers" :key="index">{{driver.full_name}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <!-- <totalSales></totalSales> -->
                <driver :driver_id="report.driver_id" ref="driverReport"></driver>
            </div>
        </div>
    </div>
</template>


<script>
import { mapState } from "vuex";
import totalSales from "../charts/totalSales.vue";
import driver from "./driver.vue";
export default {
    components: {
        totalSales,
        driver
    },
    data() {
        return {
            report: {
				type: "driver",
				driver_id:null
            },
			errors: {},
			drivers:[]
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "reports");
        this.$store.commit("changeCurrentMenu", "reportsMenu");

        
    },
    mounted() {
		this.$store.dispatch("getOrdersCount");
        this.getAllDrivers();
        
        //If refreshed the page
        if (this.$route.query.report_type != undefined) {
            this.report.type = this.$route.query.report_type
            if(this.$route.query.driver_id != undefined) {
                this.report.driver_id = this.$route.query.driver_id
                this.getDriversReport();
            }
        }
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
		getReport(){

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
		}
    },
    computed: {
        ...mapState(["ordersCount"])
    },
    watch: {}
};
</script>