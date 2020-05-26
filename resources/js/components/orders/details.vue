<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="heading-small text-muted">Booking Information</h6>
                        </div>
                        <!-- <div class="col-4 text-right">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                v-b-modal.orderTimeline
                            >Timeline</button>
                        </div> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Customer Name</label>
                                    <br />
                                    <span>{{details.customer_full_name}}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Order Status</label>
                                    <br />
                                    <span>{{details.status_str}}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Order Type</label>
                                    <br />
                                    <span>{{details.order_type}}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Assigned Driver</label>
                                    <br />
                                    <span
                                        v-if="details.driver_full_name"
                                    >{{details.driver_full_name}}</span>
                                    <span v-else>Not Assigned</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template>
                        <hr class="my-4" />
                        <h6 class="heading-small text-muted mb-4">Invoice</h6>
                        
                        <div class="float-left">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Payment Methodk</th>
                                        <td>{{details.payment_method}}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Cost</th>
                                        <td>{{details.pricing_unit}} {{details.total_cost}}</td>
                                    </tr>
                                    <tr>
                                        <th>Additional Cost</th>
                                        <td>{{details.pricing_unit}} {{details.additional_price}}</td>
                                    </tr>
                                    <tr>
                                        <th>Coupon Discount</th>
                                        <td>{{details.pricing_unit}} {{details.coupon_discount}}</td>
                                    </tr>
                                    <tr>
                                        <th>VAT ({{details.VAT_percentage}}%)</th>
                                        <td>{{details.pricing_unit}}  {{details.VAT}}</td>
                                    </tr>
                                    <tr>
                                        <th>Grand Total</th>
                                        <td>{{details.pricing_unit}}  {{details.grand_total}}</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </template>
                </div>
                <timeline></timeline>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card bg-gradient-default shadow">
                <div class="card-header bg-transparent">
                    <h3 class="mb-0 text-white">Route</h3>
                </div>
                <div class="card-body">
                    <div
                        class="timeline timeline-one-side"
                        data-timeline-content="axis"
                        data-timeline-axis-style="dashed"
                    >
                        <div class="timeline-block" v-if="details.pick_location">
                            <span class="timeline-step badge-success">
                                <i class="ni ni-map-big"></i>
                            </span>
                            <div class="timeline-content">
                                <small class="text-light font-weight-bold">Pick Location</small>
                                <h5 class="text-white mt-3 mb-0">{{details.pick_location.name}}</h5>
                                <small
                                    class="text-white mt-3 mb-0"
                                >{{details.pick_location.sub_name}}</small>
                                <p
                                    class="text-light text-sm mt-1 mb-0"
                                >{{details.pick_location.info}}</p>
                                <div class="mt-3">
                                    <span class="badge badge-pill badge-success">Location Added</span>
                                    <span
                                        class="badge badge-pill badge-success"
                                    >{{details.pick_location.created_at}}</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="timeline-block"
                            v-for="(location,index) in details.additional_locations"
                            :key="index"
                        >
                            <span class="timeline-step badge-danger">
                                <i class="ni ni-map-big"></i>
                            </span>
                            <div class="timeline-content">
                                <small class="text-light font-weight-bold">Additional Drop Locations</small>
                                <h5 class="text-white mt-3 mb-0">{{location.name}}</h5>
                                <small class="text-white mt-3 mb-0">{{location.sub_name}}</small>
                                <p class="text-light text-sm mt-1 mb-0">{{location.info}}</p>
                                <div class="mt-3">
                                    <span class="badge badge-pill badge-danger">Location Added</span>
                                    <span
                                        class="badge badge-pill badge-danger"
                                    >{{getDate(location.created_at)}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-block" v-if="details.drop_location">
                            <span class="timeline-step badge-danger">
                                <i class="ni ni-map-big"></i>
                            </span>
                            <div class="timeline-content">
                                <small class="text-light font-weight-bold">Final Destination</small>
                                <h5 class="text-white mt-3 mb-0">{{details.drop_location.name}}</h5>
                                <small
                                    class="text-white mt-3 mb-0"
                                >{{details.drop_location.sub_name}}</small>
                                <p
                                    class="text-light text-sm mt-1 mb-0"
                                >{{details.drop_location.info}}</p>
                                <div class="mt-3">
                                    <span class="badge badge-pill badge-danger">Location Added</span>
                                    <span
                                        class="badge badge-pill badge-danger"
                                    >{{getDate(details.drop_location.created_at)}}</span>
                                    <template v-if="details.drop_timestamp">
                                        <span class="badge badge-pill badge-danger">Drop Time</span>
                                        <span
                                            class="badge badge-pill badge-danger"
                                        >{{getDate(details.drop_timestamp)}}</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" v-if="details.booking_added_time.length">
            <div class="card bg-gradient-default shadow">
                <div class="card-header bg-transparent">
                    <h3 class="mb-0 text-white">Additional Time</h3>
                </div>
                <div class="card-body">
                    <div
                        class="timeline timeline-one-side"
                        data-timeline-content="axis"
                        data-timeline-axis-style="dashed"
                    >
                        <div
                            class="timeline-block"
                            v-for="(time,index) in details.booking_added_time"
                            :key="index"
                        >
                            <span class="timeline-step badge-success">
                                <i class="ni ni-map-big"></i>
                            </span>
                            <div class="timeline-content">
                                <small
                                    class="text-light font-weight-bold"
                                >{{getDate(time.created_at)}}</small>
                                <h5 class="text-white mt-3 mb-0">Added {{time.minutes}} minutes</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import timeline from "./timeline.vue";
import { settings } from "../../config/settings";

export default {
    components: {
        timeline
    },
    data() {
        return {
            orderID: ""
        };
    },
    created() {
        if (this.$route.query.orderID === undefined) {
            this.$router.push({ name: "jobs" });
        } else {
            this.orderID = this.$route.query.orderID;
        }
        this.$store.commit("changeCurrentPage", "orderDetails");
        this.$store.commit("changeCurrentMenu", "ordersMenu");
        this.$store.commit("setOrderDetails", {});
    },
    mounted() {
        this.$store.dispatch("getOrderDetails", this.orderID);
    },
    methods: {
        getDate(date) {
            return new Date(date);
        }
    },
    computed: {
        details() {
            return this.$store.getters.orderDetails;
        }
    }
};
</script>

<style type="text/css">
#orderTimeline .modal-body {
    padding: 0;
}
#orderTimeline .card {
    margin-bottom: 0;
}
</style>