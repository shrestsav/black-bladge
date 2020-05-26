<template>
  <div class="card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-8">
          <h6 class="heading-small text-muted">Booking Information</h6>
        </div>
        <div class="col-4 text-right">
          <button type="button" class="btn btn-primary btn-sm" v-b-modal.orderTimeline>Timeline</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="pl-lg-4">
        <div class="row" v-if="details">
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Customer Name</label>
              <br>
              <span>{{details.customer_full_name}}</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Order Status</label>
              <br>
              <span>{{details.status_str}}</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Order Type</label>
              <br>
              <span>{{details.order_type}}</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Assigned Driver</label>
              <br>
              <span v-if="details.driver_full_name">{{details.driver_full_name}}</span>
              <span v-else>Not Assigned</span>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label class="form-control-label">Pickup Location</label>
              <br>
              <span>{{details.pick_location.name}} ({{details.pick_location.sub_name}})</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Pickup Timestamp</label>
              <br>
              <span>{{details.pick_timestamp}}</span>
            </div>
          </div>
          <div class="col-lg-3" v-if="details.drop_location">
            <div class="form-group">
              <label class="form-control-label">Drop Location</label>
              <br>
              <span>{{details.drop_location.name}}</span>
            </div>
          </div>
          <div class="col-lg-3" v-if="details.drop_timestamp">
            <div class="form-group">
              <label class="form-control-label">Drop Timestamp</label>
              <br>
              <span>{{details.drop_timestamp}}</span>
            </div>
          </div>
        </div>
      </div>
      <!-- <template v-if="invoice && invoice.items_details.length">
        <hr class="my-4"/>
        <h6 class="heading-small text-muted mb-4">Invoice</h6>
        <div class="row">
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Service : </label>
              <span>{{invoice.invoice_details.service}}</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Payment Type : </label>
              <span>{{details.details.payment_type_name}}</span>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label class="form-control-label">Delivery Type : </label>
              <span>{{invoice.invoice_details.order_type}}</span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>S.No.</th>
                <th>Items</th>
                <th>Quantity</th>
                <th>Service Charge (AED)</th>
                <th>Item Charge (AED)</th>
                <th>Amount (AED)</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody class="list">
              <tr  v-for="(item,index) in invoice.items_details" :key="index">
                <td>{{index+1}}</td>
                <td>{{item.item}}</td>
                <td>{{item.quantity}}</td>
                <td>{{item.service_charge}}</td>
                <td>{{item.item_charge}}</td>
                <td>{{item.total}}</td>
                <td>{{item.remarks}}</td>
              </tr>
            </tbody>
          </table>
          <br>
        </div>
        <div class="float-left">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr v-if="invoice.invoice_details.PDR != '' && invoice.invoice_details.PDR != null">
                <th>Order Remark</th>
                <td>{{invoice.invoice_details.PDR}}</td>     
              </tr>
              <tr>
                <th>Total Quantity</th>
                <td>{{invoice.invoice_details.total_quantity}}</td>     
              </tr>
              <tr>
                <th>Total Amount</th>
                <td>AED {{invoice.invoice_details.total_amount}}</td>
              </tr>
              <tr v-if="invoice.invoice_details.coupon_discount">
                <th>Coupon Discount</th>
                <td>AED {{invoice.invoice_details.coupon_discount}}</td>
              </tr>
              <tr>
                <th>VAT ({{invoice.invoice_details.VAT_percent}}%)</th>
                <td>AED {{invoice.invoice_details.VAT}}</td>                    
              </tr>
              <tr>
                <th>Delivery Charge</th>
                <td>AED {{invoice.invoice_details.delivery_charge}}</td>        
              </tr>
              <tr>
                <th>Grand Total</th>
                <td>AED {{invoice.invoice_details.grand_total}}</td>
              </tr>
            </thead>
          </table>
        </div>
      </template> -->
    </div>
    <timeline></timeline>
  </div>
</template>

<script>

  import timeline from './timeline.vue'
  import {settings} from '../../config/settings'

  export default{
    components:{
      timeline
    },
    data(){
      return{
        orderID:'',
      }
    },
    created(){
      if(this.$route.query.orderID===undefined){
        this.$router.push({name:'jobs'});
      }
      else{
        this.orderID = this.$route.query.orderID;
      }
      this.$store.commit('changeCurrentPage', 'orderDetails')
      this.$store.commit('changeCurrentMenu', 'ordersMenu')
      this.$store.commit('setOrderDetails',{})
    },
    mounted(){
      this.$store.dispatch('getOrderDetails',this.orderID)
    },
    methods:{
    },
    computed: {
      details(){
        return this.$store.getters.orderDetails
      }
    }

  }

</script>

<style type="text/css">
  #orderTimeline .modal-body {
    padding: 0;
  }
  #orderTimeline .card {
    margin-bottom: 0;
  }
</style>