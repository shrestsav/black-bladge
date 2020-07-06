<template>
  <div class="vue-component-body">
    <div class="container count-container">
      <div class="row">
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card count-card">
            <div class="">
              <div class="mtb-auto float-left">
              <div class="count-header"> Unassigned Count</div>
              <div class="count-value">{{ unassignedCount }}</div>
            </div>
            <div class="icon mtb-auto icon float-right mtb-auto">
              <i class="ni ni-single-02"></i>
            </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card count-card">
            <div class="">
              <div class="mtb-auto float-left">
                <div class="count-header">Assinged Orders</div>
                <div class="count-value">
                  {{ assignedCount }}
                </div>
              </div>
              <div class="icon float-right mtb-auto">
                <i class="ni ni-active-40"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card count-card">
            <div class="">
               <div class="mtb-auto float-left">
                <div class="count-header">Active Orders</div>
                <div class="count-value">
                  {{ activeCount }}
                </div>
              </div>
              <div class="icon float-right mtb-auto">
                <i class="ni ni-delivery-fast"></i>
              </div>
            </div>
          </div>
        </div>        
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card count-card">
            <div class="">
              <div class="mtb-auto float-left">
                <div class="count-header"> Completed Count</div>
                <div class="count-value">
                  {{ completedCount }} 
                </div>
              </div>
              <div class="icon float-right mtb-auto">
                <i class="ni ni-books"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-2rem">
        <div class="col-12">
          <label for="filter">View Report on basis of</label>
          <select  class="form-control" v-model="reportType" @change="getReport()">
            <option value="PastWeek">
              Past Week
            </option>
            <option value="PastMonth">
              Past Month
            </option>
            <option value="PastYear">
              Past Year
            </option>
            <option value="AllTime">
              All Time 
            </option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card cust-doughnut">
            <div class="">
              <vc-donut v-bind="getUnAssignedDougnnut">
                  <div class="doughnut-text">Unassigned</div>
              </vc-donut>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card cust-doughnut">
            <div class="">
              <vc-donut v-bind="getAssignedDoughnutSection">
                  <div class="doughnut-text">Assigned</div>
              </vc-donut>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card cust-doughnut">
            <div class="">
              <vc-donut v-bind="getActiveDoughnutSection">
                  <div class="doughnut-text">Active</div>
              </vc-donut>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
          <div class="card cust-doughnut">
            <div class="">
              <vc-donut v-bind="getCompletedeDoughnutSection">
                  <div class="doughnut-text">Completed</div>
              </vc-donut>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data(){
    return {
      reportType: 'PastWeek',
      activeCount: 0,
      unassignedCount: 0,
      assignedCount:0,
      completedCount: 0,
      reachingCount: 0,
      arrivedCount: 0,
      startedCount: 0,
      reachedCount: 0,
      total:0,
      props: {
        size: 150,
        sections: [
        	{ label: "Reaching",value: 35 ,color: 'yellow' },
          { label: "Arrived",value: 15 ,color: 'orange'},
          { label: "Started",value: 15 ,color: 'green'},
          { label: "Reached",value: 35 ,color: '#f9f9f9'}
        ],
        thickness: 20, // set this to 100 to render it as a pie chart instead
        hasLegend: true,
        // specify more props here
      }
    }
  },
  created(){
    this.getCountValue();
  },
  methods: {
    resetCounter:function(){
      this.activeCount = 0;
      this.unassignedCount = 0;
      this.assignedCount = 0;
      this.completedCount = 0;
      this.reachingCount = 0;
      this.arrivedCount = 0;
      this.startedCount = 0;
      this.reachedCount = 0;
      this.total = 0;
    },
    getCountValue(){
      this.resetCounter();
      axios.get(`/get-count?type=${this.reportType}`).then((data) => {
        if(!!data.data && data.data.length > 0){
          this.setCountValues(data.data);
        }
      })
    },
    getReport(){
      this.getCountValue();
    },
    setCountValues(dataArray){
      this.resetCounter();
      dataArray.forEach(element => {
        this.total += element.count;
          switch(true){
            case (element.status == 1): 
              this.assignedCount = element.count;
              break;
            case (element.status == 2):
              this.reachedCount = element.count;
              this.activeCount += element.count;
              break;
            case (element.status == 3):
              this.arrivedCount = element.count;
              this.activeCount += element.count;
              break;
            case (element.status == 4):
              this.startedCount = element.count;
              this.activeCount += element.count;
              break;
            case (element.status == 5):
              this.reachedCount = element.count;
              this.activeCount += element.count;
              break;
            case (element.status == 6):
              this.completedCount  = element.count;
              break;
            case(element.status == 0):
              this.unassignedCount = element.count;
              break;
          }
      });
    }
  },
  computed:{
    getActiveDoughnutSection(){
      return {
        size: 150,
        sections: [
        	{ label: "Reaching",value: Math.floor((this.reachingCount/ this.activeCount ) *100) ,color: 'yellow' },
          { label: "Arrived",value: Math.floor((this.arrivedCount/ this.activeCount ) *100),color: 'orange'},
          { label: "Started",value: Math.floor((this.startedCount/ this.activeCount ) *100),color: 'green'},
          { label: "Reached",value: Math.floor((this.reachedCount/ this.activeCount ) *100),color: '#f9f9f9'}
        ],
        thickness: 20, // set this to 100 to render it as a pie chart instead
        hasLegend: true,
        // specify more props here
      }
    },
    getUnAssignedDougnnut(){
      return {
        size: 150,
        sections: [
          { label: "unassigned",value: Math.floor((this.unassignedCount/ this.total ) *100) ,color: 'green'},
        ],
        thickness: 20, // set this to 100 to render it as a pie chart instead
        hasLegend: true,
        // specify more props here
      }
    },
    getAssignedDoughnutSection(){
       return {
        size: 150,
        sections:  [
          { label: "Assigned",value: Math.floor((this.assignedCount/ this.total ) *100) ,color: 'green'},
        ],
        thickness: 20, // set this to 100 to render it as a pie chart instead
        hasLegend: true,
        // specify more props here
      }
    },
     getCompletedeDoughnutSection(){
       return {
        size: 150,
        sections:  [
        	{ label: "Reaching",value: Math.floor((this.completedCount/ this.total ) *100 ),color: 'yellow' },
        ],
        thickness: 20, // set this to 100 to render it as a pie chart instead
        hasLegend: true,
        // specify more props here
      }
    },
  }
  
}
</script>
<style scoped>
  .card{
    border: 0;
    box-shadow: 0 0 2rem 0px rgba(136, 152, 170, 0.54);
    padding: 1rem;
  }
  .count-card{
    height: 5rem;

  }
  .count-container{
    margin-top: 1.4rem;
  }
  .count-header{
    font-size: 0.84rem;
    color: #999998;
    font-weight: 800;
    text-decoration: double;
    text-transform: uppercase;
  }
  .count-value{
    font-size: 0.6rems;
  }
  .mtb-auto{
    margin-top: auto;
    margin-bottom: auto;
  }
  .doughnut-text{
    font-size: 1rem;
    color: #9999a5;
    font-weight: 800;
    text-transform: uppercase;
  }
</style>
<style>
.cust-doughnut{
  min-height: 20rem;
}
.mb-2rem{
  margin-bottom: 2rem;
}
</style>